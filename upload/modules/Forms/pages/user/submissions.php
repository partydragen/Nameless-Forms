<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Forms module - user submission page
 */
 
// Must be logged in
if (!$user->isLoggedIn()) {
    Redirect::to(URL::build('/'));
}

// Always define page name for navbar
define('PAGE', 'cc_submissions');
$page_title = $language->get('user', 'user_cp');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$forms = new Forms();

// Get user groups
$user_groups = $user->getAllGroupIds();
$group_ids = implode(',', $user_groups);

$timeago = new TimeAgo(TIMEZONE);

if (!isset($_GET['view'])) {
    $submissions = [];
    $submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE user_id = ? AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view_own = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC', [$user->data()->id])->results();

    if (count($submissions_query)) {
        // Get page
        if (isset($_GET['p'])) {
            if (!is_numeric($_GET['p'])) {
                Redirect::to(URL::build('/user/submissions/'));
            } else {
                if ($_GET['p'] == 1) {
                    // Avoid bug in pagination class
                    Redirect::to(URL::build('/user/submissions/'));
                }
                $p = $_GET['p'];
            }
        } else {
            $p = 1;
        }

        $paginator = new Paginator((isset($template_pagination) ? $template_pagination : []));
        $results = $paginator->getLimited($submissions_query, 10, $p, count($submissions_query));
        $pagination = $paginator->generate(7, URL::build('/user/submissions/'));

        // Get all submissions
        foreach ($results->data as $submission) {
            $form = new Form($submission->form_id);
            $status = new Status($submission->status_id);

            // Check if last updater is anonymous
            if ($submission->updated_by != 0) {
                $updated_by_user = new User($submission->updated_by);

                $updated_by_name = $updated_by_user->getDisplayname();
                $updated_by_profile = $updated_by_user->getProfileURL();
                $updated_by_style = $updated_by_user->getGroupClass();
                $updated_by_avatar = $updated_by_user->getAvatar();
            } else {
                $updated_by_name = $forms_language->get('forms', 'anonymous');
                $updated_by_profile = null;
                $updated_by_style = null;
                $updated_by_avatar = null;
            }

            $submissions[] = [
                'id' => $submission->id,
                'form_name' => $form->data()->title,
                'status' => $status->data()->html,
                'created_at' => $timeago->inWords($submission->created, $language),
                'updated_by_name' => $updated_by_name,
                'updated_by_profile' => $updated_by_profile,
                'updated_by_style' => $updated_by_style,
                'updated_by_avatar' => $updated_by_avatar,
                'updated_at' => $timeago->inWords($submission->updated, $language),
                'link' => URL::build('/user/submissions/', 'view=' . $submission->id),
            ];
        }

        $smarty->assign('PAGINATION', $pagination);
    }

    $smarty->assign([
        'SUBMISSIONS_LIST' => $submissions,
        'VIEW' => $language->get('general', 'view'),
        'NO_SUBMISSIONS' => $forms_language->get('forms', 'no_open_submissions'),
        'FORM' => $forms_language->get('forms', 'form'),
        'USER' => $forms_language->get('forms', 'user'),
        'UPDATED_BY' => $forms_language->get('forms', 'updated_by'),
        'STATUS' => $forms_language->get('forms', 'status')
    ]);

    $template_file = 'forms/submissions.tpl';
} else {
    // Get submission by id
    $submission = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE id = ? AND user_id = ? AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view_own = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC', [$_GET['view'], $user->data()->id]);
    if (!$submission->count()) {
        Redirect::to(URL::build('/user/submissions'));
    }
    $submission = new Submission(null, null, $submission->first());

    $form = new Form($submission->data()->form_id);
    $status = new Status($submission->data()->status_id);

    // Check input
    if (Input::exists()) {
        $errors = [];

        // Check token
        if (Token::check(Input::get('token'))) {
            if ($status->data()->open) {
                // Valid token
                $validation = Validate::check($_POST, [
                    'content' => [
                        Validate::REQUIRED => true,
                        Validate::MIN => 3,
                        Validate::MAX => 10000
                    ]
                ])->messages([
                    'content' => [
                        Validate::REQUIRED => $forms_language->get('forms', 'comment_minimum'),
                        Validate::MIN => $forms_language->get('forms', 'comment_minimum'),
                        Validate::MAX => $forms_language->get('forms', 'comment_maximum')
                    ]
                ]);

                if ($validation->passed()) {
                    DB::getInstance()->insert('forms_comments', [
                        'form_id' => $submission->data()->id,
                        'user_id' => $user->data()->id,
                        'created' => date('U'),
                        'content' => Output::getClean(nl2br(Input::get('content')))
                    ]);

                    // Update status on comment?
                    $status_id = $submission->data()->status_id;
                    if ($form->data()->comment_status != 0) {
                        $status_id = $form->data()->comment_status;
                    }

                    DB::getInstance()->update('forms_replies', $submission->data()->id, [
                        'updated_by' => $user->data()->id,
                        'updated' => date('U'),
                        'status_id' => $status_id
                    ]);

                    EventHandler::executeEvent('updatedFormSubmission', [
                        'event' => 'updatedFormSubmission',
                        'username' => Output::getClean($form->data()->title),
                        'content' => $forms_language->get('forms', 'updated_submission_text', [
                            'form' => $form->data()->title,
                            'user' => $user->getDisplayname()
                        ]),
                        'content_full' => Output::getClean(Input::get('content')),
                        'avatar_url' => $user->getAvatar(128, true),
                        'title' => Output::getClean($form->data()->title),
                        'url' => rtrim(Util::getSelfURL(), '/') . URL::build('/panel/forms/submissions/', 'view=' . $submission->data()->id)
                    ]);

                    $success = $language->get('moderator', 'comment_created');

                    Session::flash('submission_success', $forms_language->get('forms', 'submission_updated'));
                    Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission->data()->id)));
                } else {
                    // Validation errors
                    $errors = $validation->errors();
                }
            }
        } else {
            // Invalid token
            $errors[] = $language->get('general', 'invalid_token');
        }
    }

    // Get comments
    $comments = DB::getInstance()->get('forms_comments', ['form_id', '=', $submission->data()->id])->results();
    $smarty_comments = [];
    foreach ($comments as $comment) {
        // Check if comment user is 
        if ($comment->anonymous != 1) {
            $comment_user = new User($comment->user_id);

            $user_name = $comment_user->getDisplayname();
            $user_profile = $comment_user->getProfileURL();
            $user_style = $comment_user->getGroupClass();
            $user_avatar = $comment_user->getAvatar();
        } else {
            $user_name = $forms_language->get('forms', 'anonymous');
            $user_profile = null;
            $user_style = null;
            $user_avatar = null;
        }

        $smarty_comments[] = [
            'username' => $user_name,
            'profile' => $user_profile,
            'style' => $user_style,
            'avatar' => $user_avatar,
            'content' => Output::getPurified(Output::getDecoded($comment->content)),
            'date' => date(DATE_FORMAT, $comment->created),
            'date_friendly' => $timeago->inWords($comment->created, $language)
        ];
    }

    $target_user = new User($submission->data()->user_id);
    $smarty->assign([
        'FORM_X' => $forms_language->get('forms', 'form_x', ['form' => $form->data()->title]),
        'CURRENT_STATUS_X' => $forms_language->get('forms', 'current_status_x', ['status' => $status->data()->html]),
        'LAST_UPDATED' => $forms_language->get('forms', 'last_updated'),
        'LAST_UPDATED_DATE' => date(DATE_FORMAT, $submission->data()->updated),
        'LAST_UPDATED_FRIENDLY' => $timeago->inWords($submission->data()->updated, $language),
        'USER' => $target_user->getDisplayname(),
        'USER_PROFILE' => $target_user->getProfileURL(),
        'USER_STYLE' => $target_user->getGroupClass(),
        'USER_AVATAR' => $target_user->getAvatar(),
        'CREATED_DATE' => date(DATE_FORMAT, $submission->data()->created),
        'CREATED_DATE_FRIENDLY' => $timeago->inWords($submission->data()->created, $language),
        'COMMENTS' => $smarty_comments,
        'COMMENTS_TEXT' => $language->get('moderator', 'comments'),
        'CAN_COMMENT' => Output::getClean($status->data()->open),
        'NEW_COMMENT' => $language->get('moderator', 'new_comment'),
        'NO_COMMENTS' => $language->get('moderator', 'no_comments'),
        'ANSWERS' => $submission->getFieldsAnswers(),
        'TOKEN' => Token::get(),
        'SUBMIT' => $language->get('general', 'submit'),
        'PATH_TO_UPLOADS' => ((defined('CONFIG_PATH')) ? CONFIG_PATH . '/' : '/') . 'uploads/forms_submissions/'
    ]);

    $template_file = 'forms/submissions_view.tpl';
}

// Language values
$smarty->assign([
    'USER_CP' => $language->get('user', 'user_cp'),
    'SUBMISSIONS' => $forms_language->get('forms', 'submissions'),
]);


// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('submission_success'))
    $success = Session::flash('submission_success');

if (isset($success))
    $smarty->assign([
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ]);

if (isset($errors) && count($errors))
    $smarty->assign([
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ]);

require(ROOT_PATH . '/core/templates/cc_navbar.php');

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate($template_file, $smarty);
