<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Forms module - panel form page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('forms.view-submissions')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'forms');
define('PANEL_PAGE', 'submissions');
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

require_once(ROOT_PATH . '/modules/Forms/classes/Forms.php');
$forms = new Forms();

// Get user groups
$user_groups = $user->getAllGroupIds();
$group_ids = implode(',', $user_groups);

$timeago = new Timeago(TIMEZONE);

$url_path = '';
if(!isset($_GET['view'])){
    // Check input
    if(Input::exists()){
        $errors = array();
        
        // Check token
        if(Token::check(Input::get('token'))){
            // Valid token
            $form = $_POST['form_selection'];
            $status = $_POST['status_selection'];
            $target_user = $_POST['user'];
            
            if ($form != 0) {
                $url_path .= 'form='.$form.'&';
            }
            if ($status != 0) {
                $url_path .= 'status='.$status.'&';
            }
            if (!empty($target_user)) {
                if(is_numeric($target_user)) {
                    $url_path .= 'user='.$target_user.'&';
                } else {
                    $user_query = DB::getInstance()->query('SELECT id FROM nl2_users WHERE username = ?', array(Output::getClean($target_user)));
                    if($user_query->count()) {
                        $url_path .= 'user='.$user_query->first()->id . '&';
                    }
                }
            }
            
            Redirect::to(URL::build('/panel/forms/submissions/', $url_path));
            die();
        } else {
            // Invalid token
            $errors[] = $language->get('general', 'invalid_token');
        }
    }
    
    $query = 'SELECT * FROM nl2_forms_replies';
    $where = ' WHERE form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view = 1 AND group_id IN('.$group_ids.'))';
    $order = ' ORDER BY created DESC';
    $limit = '';
    $params = array();
    $url_parameters = '';
    
    if(!isset($_GET['form']) && !isset($_GET['status']) && !isset($_GET['user'])){
        // sort by open submissions
        $url_parameters = true;
        $where .= ' AND status_id IN (SELECT id FROM nl2_forms_statuses WHERE open = 1)';
    } else {
        if(isset($_GET['form'])) {
            $url_parameters .= 'form=' . $_GET['form'] . '&';
            $where .= ' AND form_id = ?';
            array_push($params, $_GET['form']);
        }
        
        if(isset($_GET['status'])) {
            $url_parameters .= 'status=' . $_GET['status'] . '&';
            $where .= ' AND status_id = ?';
            array_push($params, $_GET['status']);
        }
        
        if(isset($_GET['user'])) {
            $url_parameters .= 'user=' . $_GET['user'] . '&';
            $where .= ' AND user_id = ?';
            array_push($params, $_GET['user']);
        }
    }
    
    $submissions_query = DB::getInstance()->query($query . $where . $order . $limit, $params)->results();
    
    $submissions = array();
    if(count($submissions_query)){
        // Get page
        if(isset($_GET['p'])){
            if(!is_numeric($_GET['p'])){
                Redirect::to(URL::build('/panel/forms/submissions/', $url_parameters));
                die();
            } else {
                if($_GET['p'] == 1){
                    // Avoid bug in pagination class
                    Redirect::to(URL::build('/panel/forms/submissions/', $url_parameters));
                    die();
                }
                $p = $_GET['p'];
            }
        } else {
            $p = 1;
        }
        
        $paginator = new Paginator((isset($template_pagination) ? $template_pagination : array()));
        $results = $paginator->getLimited($submissions_query, 10, $p, count($submissions_query));
        $pagination = $paginator->generate(7, URL::build('/panel/forms/submissions/', $url_parameters));
        
        // Get all submissions
        foreach($results->data as $submission){
            $form = $queries->getWhere('forms', array('id', '=', $submission->form_id));
            $form = $form[0];
            
            // get current status from id
            $status = $queries->getWhere('forms_statuses', array('id', '=', $submission->status_id));
            $status = $status[0];
            
            // Is user a guest or a user
            if($submission->user_id == null){
                $user_name = $forms_language->get('forms', 'guest');
                $user_profile = null;
                $user_style = null;
                $user_avatar = null;
            } else {
                $target_user = new User($submission->user_id);
                
                $user_name = $target_user->getDisplayname();
                $user_profile = URL::build('/panel/user/' . Output::getClean($submission->user_id . '-' . $target_user->getDisplayname(true)));
                $user_style = $target_user->getGroupClass();
                $user_avatar = $target_user->getAvatar();
            }
            
            // Is user a guest or a user
            if($submission->updated_by == null || $submission->updated_by == 0){
                $updated_by_name = ($submission->updated_by == null ? $forms_language->get('forms', 'guest') : $forms_language->get('forms', 'anonymous'));
                $updated_by_profile = null;
                $updated_by_style = null;
                $updated_by_avatar = null;
            } else {
                $updated_by_user = new User($submission->updated_by);
                
                $updated_by_name = $updated_by_user->getDisplayname();
                $updated_by_profile = URL::build('/panel/user/' . Output::getClean($submission->updated_by . '-' . $updated_by_user->getDisplayname(true)));
                $updated_by_style = $updated_by_user->getGroupClass();
                $updated_by_avatar = $updated_by_user->getAvatar();
            }
            
            $submissions[] = array(
                'id' => $submission->id,
                'form_name' => $form->title,
                'status' => $status->html,
                'user_name' => $user_name,
                'user_profile' => $user_profile,
                'user_style' => $user_style,
                'user_avatar' => $user_avatar,
                'created_at' => $timeago->inWords(date('Y-m-d H:i:s', $submission->created), $language->getTimeLanguage()),
                'updated_by_name' => $updated_by_name,
                'updated_by_profile' => $updated_by_profile,
                'updated_by_style' => $updated_by_style,
                'updated_by_avatar' => $updated_by_avatar,
                'updated_at' => $timeago->inWords(date('Y-m-d H:i:s', $submission->updated), $language->getTimeLanguage()),
                'link' => URL::build('/panel/forms/submissions/', 'view=' . $submission->id),
            );
        }
        
        $smarty->assign('PAGINATION', $pagination);
        
    }
    
    // Get forms from database
    $forms_query = $queries->orderAll('forms', 'id', 'ASC');
    $forms_array = array();
    if(count($forms_query)){
        $forms_array[] = array(
            'id' => 0,
            'name' => 'All',
        );
        foreach($forms_query as $form){
            $forms_array[] = array(
                'id' => $form->id,
                'name' => Output::getClean($form->title),
            );
        }
    }
    
    // Get statuses from database
    $statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
    $status_array = array();
    if(count($statuses)){
            $status_array[] = array(
                'id' => 0,
                'html' => 'All open'
            );
        foreach($statuses as $status){
            $status_array[] = array(
                'id' => $status->id,
                'html' => $status->html
            );
        }
    }
    
    $smarty->assign(array(
        'SUBMISSIONS_LIST' => $submissions,
        'VIEW' => $language->get('general', 'view'),
        'NO_SUBMISSIONS' => $forms_language->get('forms', 'no_open_submissions'),
        'FORM' => $forms_language->get('forms', 'form'),
        'USER' => $forms_language->get('forms', 'user'),
        'UPDATED_BY' => $forms_language->get('forms', 'updated_by'),
        'STATUS' => $forms_language->get('forms', 'status'),
        'ACTIONS' => $forms_language->get('forms', 'actions'),
        'FORM_LIST' => $forms_array,
        'STATUS_LIST' => $status_array,
        'FORM_SELECTION_VALUE' => (isset($_GET['form']) ? $_GET['form'] : '0'),
        'STATUS_SELECTION_VALUE' => (isset($_GET['status']) ? $_GET['status'] : '0'),
        'USER' => $forms_language->get('forms', 'user'),
        'ID_OR_USERNAME' => $forms_language->get('forms', 'id_or_username'),
        'USER_VALUE' => (isset($_GET['user']) ? $_GET['user'] : ''),
        'SORT' => $forms_language->get('forms', 'sort'),
    ));
            
    $template_file = 'forms/submissions.tpl';
} else {
    if(!isset($_GET['action'])){
        // Get submission by id
        $submission = DB::getInstance()->query('SELECT nl2_forms_replies.*, nl2_forms.title AS form_title, nl2_forms_statuses.html AS status_html FROM nl2_forms_replies LEFT JOIN nl2_forms_statuses ON status_id=nl2_forms_statuses.id LEFT JOIN nl2_forms ON form_id=nl2_forms.id WHERE nl2_forms_replies.id = ?', array($_GET['view']))->results();
        if(!count($submission)){
            Redirect::to(URL::build('/panel/forms/submissions'));
            die();
        }
        $submission = $submission[0];
        
        // Does user have permission to view this submission
        if(!$forms->canViewSubmission($group_ids, $submission->form_id)){
            Redirect::to(URL::build('/panel/forms/submissions'));
            die();
        }
        
        // Check input
        if(Input::exists()){
            $errors = array();

            // Check token
            if(Token::check(Input::get('token'))){
                // Valid token
                $validate = new Validate();

                $validation = $validate->check($_POST, array(
                    'content' => array(
                        'max' => 10000
                    )
                ));

                if($validation->passed()){
                    $any_changes = false;
                    
                    // Submit as anonymous?
                    if(isset($_POST['anonymous']) && $_POST['anonymous'] == 'on') $anonymous = 1;
                    else $anonymous = 0;
                    
                    // Send notify email?
                    if(isset($_POST['notify_email']) && $_POST['notify_email'] == 'on') $sendEmail = 1;
                    else $sendEmail = 0;
                    
                    // Check if status have changed
                    $status_id = $submission->status_id;
                    $status_html = $submission->status_html;
                    if($submission->status_id != $_POST['status']) {
                        $status_query = $queries->getWhere('forms_statuses', array('id', '=', $_POST['status']));
                        if(count($status_query)){
                            $groups = explode(',', $status_query[0]->gids);
                            $hasperm = false;
                            foreach ($user_groups as $group_id) {
                                if(in_array($group_id, $groups)) {
                                    $hasperm = true;
                                    break;
                                }
                            }
                            
                            if($hasperm) {
                                $status_html = $status_query[0]->html;
                                $status_id = $_POST['status'];
                                $any_changes = true;
                            }
                        }
                    }
                    
                    if(!empty(Input::get('content'))) {
                        $any_changes = true;
                        $queries->create('forms_comments', array(
                            'form_id' => $submission->id,
                            'user_id' => $user->data()->id,
                            'created' => date('U'),
                            'anonymous' => $anonymous,
                            'content' => Output::getClean(nl2br(Input::get('content')))
                        ));
                    }

                    // Was there any changes?
                    if($any_changes == true) {
                        $queries->update('forms_replies', $submission->id, array(
                            'updated_by' => ($anonymous != 1 ? $user->data()->id : 0),
                            'updated' => date('U'),
                            'status_id' => $status_id
                        ));
                        
                        // Alert user?
                        if($submission->user_id != null) {
                            $target_user = new User($submission->user_id);
                            if($target_user && $forms->canViewOwnSubmission(implode(',', $user->getAllGroupIds()), $submission->form_id)) {
                                // Send alert to user
                                Alert::create(
                                    $submission->user_id,
                                    'submission_update',
                                    array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($submission->form_title))),
                                    array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($submission->form_title))),
                                    URL::build('/user/submissions/', 'view=' . Output::getClean($submission->id))
                                );
                                
                                // Send email to user of new changes to submission
                                if($sendEmail == 1) {
                                    $link = rtrim(Util::getSelfURL(), '/') . URL::build('/user/submissions/', 'view=' . $submission->id);
                                    $comment = (!empty(Input::get('content')) ? Input::get('content') : $forms_language->get('forms', 'no_comment'));
                                    $subject = SITE_NAME . ' - ' . str_replace('{x}', Output::getClean($submission->form_title), $forms_language->get('forms', 'submission_updated_subject'));
                                    $message = str_replace(array('{form}', '{status}', '{updated_by}', '{comment}', '{link}'), array(Output::getClean($submission->form_title), $status_html, ($anonymous == 0 ? $user->getDisplayname() : $forms_language->get('forms', 'anonymous')), $comment, $link), $forms_language->get('forms', 'submission_updated_message'));
                                    
                                    $php_mailer = $queries->getWhere('settings', array('name', '=', 'phpmailer'));
                                    $php_mailer = $php_mailer[0]->value;
                                    if ($php_mailer == '1') {
                                        // PHP Mailer
                                        $email = array(
                                            'to' => array('email' => Output::getClean($target_user->data()->email), 'name' => $target_user->getDisplayname()),
                                            'subject' => $subject,
                                            'message' => $message
                                        );

                                        $sent = Email::send($email, 'mailer');

                                        if (isset($sent['error'])) {
                                            // Error, log it
                                            $queries->create('email_errors', array(
                                                'type' => 3, // 3 = forgot password
                                                'content' => $sent['error'],
                                                'at' => date('U'),
                                                'user_id' => $target_user->data()->id
                                            ));
                                        }
                                        
                                    } else {
                                        // PHP mail function
                                        $headers = 'From: ' . $siteemail . "\r\n" .
                                            'Reply-To: ' . $siteemail . "\r\n" .
                                            'X-Mailer: PHP/' . phpversion() . "\r\n" .
                                            'MIME-Version: 1.0' . "\r\n" .
                                            'Content-type: text/html; charset=UTF-8' . "\r\n";

                                        $email = array(
                                            'to' => $target_user->data()->email,
                                            'subject' => $subject,
                                            'message' => $message,
                                            'headers' => $headers
                                        );

                                        $sent = Email::send($email, 'php');

                                        if (isset($sent['error'])) {
                                            // Error, log it
                                            $queries->create('email_errors', array(
                                                'type' => 3, // 3 = forgot password
                                                'content' => $sent['error'],
                                                'at' => date('U'),
                                                'user_id' => $target_user->data()->id
                                            ));
                                        }
                                    }
                                }
                            }
                        }

                        Session::flash('submission_success', $forms_language->get('forms', 'submission_updated'));
                        Redirect::to(URL::build('/panel/forms/submissions/', 'view=' . Output::getClean($submission->id)));
                        die();
                    }
                } else {
                    // Display error
                }
            } else {
                // Invalid token
                $errors[] = $language->get('general', 'invalid_token');
            }
        }
        
        // Get answers and questions
        $answer_array = array();
        if(empty($submission->content)) {
            // New fields generation
            $fields = DB::getInstance()->query('SELECT name, value FROM nl2_forms_replies_fields LEFT JOIN nl2_forms_fields ON field_id=nl2_forms_fields.id WHERE submission_id = ?', array($submission->id))->results();
            foreach($fields as $field){
                $answer_array[] = array(
                    'question' => Output::getClean($field->name),
                    'answer' => Output::getPurified(Output::getDecoded($field->value))
                );
            }
        } else {
            // Legacy fields generation
            $answers = json_decode($submission->content, true);
            foreach($answers as $answer){
                $question = $queries->getWhere('forms_fields', array('id', '=', $answer[0]));
                $answer_array[] = array(
                    'question' => Output::getClean($question[0]->name),
                    'answer' => Output::getPurified(Output::getDecoded($answer[1]))
                );
            }
        }
        
        // Get comments
        $comments = $queries->getWhere('forms_comments', array('form_id', '=', $submission->id));
        $smarty_comments = array();
        foreach($comments as $comment){
            $comment_user = new User($comment->user_id);
            
            $smarty_comments[] = array(
                'username' => $comment_user->getDisplayname(),
                'profile' => URL::build('/panel/user/' . Output::getClean($comment->user_id . '-' . $comment_user->getDisplayname(true))),
                'style' => $comment_user->getGroupClass(),
                'avatar' => $comment_user->getAvatar(),
                'anonymous' => $comment->anonymous,
                'content' => Output::getPurified(Output::getDecoded($comment->content)),
                'date' => date('d M Y, H:i', $comment->created),
                'date_friendly' => $timeago->inWords(date('Y-m-d H:i:s', $comment->created), $language->getTimeLanguage()),
                'delete_link' => ($user->hasPermission('forms.delete-submissions') ? URL::build('/panel/forms/submissions/', 'view='.Output::getClean($submission->id).'&action=delete_comment&id=' . Output::getClean($comment->id)) : null)
            );
        }
        
        // Is user a guest or a user
        if($submission->user_id == null){
            $user_name = $forms_language->get('forms', 'guest');
            $user_profile = null;
            $user_style = null;
            $user_avatar = null;
        } else {
            $target_user = new User($submission->user_id);
            
            $user_name = $target_user->getDisplayname();
            $user_profile = URL::build('/panel/user/' . Output::getClean($submission->user_id . '-' . $target_user->getDisplayname(true)));
            $user_style = $target_user->getGroupClass();
            $user_avatar = $target_user->getAvatar();
        }
        
        // Form statuses
        $statuses = array();

        $form_statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
        if(count($form_statuses)){
            foreach($form_statuses as $status){
                $form_ids = explode(',', $status->fids);
                
                if(in_array($submission->form_id, $form_ids) || $status->id == 1){
                    // Check permissions
                    $groups = explode(',', $status->gids);
                    $perms = false;
                    foreach ($user_groups as $group_id) {
                        if(in_array($group_id, $groups)) {
                            $perms = true;
                            break;
                        }
                    }
                
                    $statuses[] = array(
                        'id' => $status->id,
                        'active' => (($submission->status_id == $status->id) ? true : false),
                        'html' => $status->html,
                        'permission' => $perms
                    );
                }
            }
        }
        
        // Can user view own submission?
        $can_view_own = false;
        if($submission->user_id != null && $target_user && $forms->canViewOwnSubmission(implode(',', $user->getAllGroupIds()), $submission->form_id)) {
            $can_view_own = true;
        }

        $smarty->assign(array(
            'FORM_X' => str_replace('{x}', Output::getClean($submission->form_title), $forms_language->get('forms', 'form_x')),
            'CURRENT_STATUS_X' => str_replace('{x}', $submission->status_html, $forms_language->get('forms', 'current_status_x')),
            'LAST_UPDATED' => $forms_language->get('forms', 'last_updated'),
            'LAST_UPDATED_DATE' => date('d M Y, H:i', $submission->updated),
            'LAST_UPDATED_FRIENDLY' => $timeago->inWords(date('Y-m-d H:i:s', $submission->updated), $language->getTimeLanguage()),
            'USER' => $user_name,
            'USER_PROFILE' => $user_profile,
            'USER_STYLE' => $user_style,
            'USER_AVATAR' => $user_avatar,
            'CREATED_DATE' => date('d M Y, H:i', $submission->created),
            'CREATED_DATE_FRIENDLY' => $timeago->inWords(date('Y-m-d H:i:s', $submission->created), $language->getTimeLanguage()),
            'COMMENTS' => $smarty_comments,
            'COMMENTS_TEXT' => $language->get('moderator', 'comments'),
            'NEW_COMMENT' => $language->get('moderator', 'new_comment'),
            'NO_COMMENTS' => $language->get('moderator', 'no_comments'),
            'ANSWERS' => $answer_array,
            'DELETE_LINK' => ($forms->canDeleteSubmission($group_ids, $submission->form_id) ? URL::build('/panel/forms/submissions/', 'view='.Output::getClean($submission->id).'&action=delete_submission&id=' . Output::getClean($submission->id)) : null),
            'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
            'CONFIRM_DELETE_SUBMISSION' => $forms_language->get('forms', 'confirm_delete_submisssion'),
            'CONFIRM_DELETE_COMMENT' => $forms_language->get('forms', 'confirm_delete_comment'),
            'YES' => $language->get('general', 'yes'),
            'NO' => $language->get('general', 'no'),
            'STATUSES' => $statuses,
            'CAN_USE_ANONYMOUS' => ($can_view_own && $user->hasPermission('forms.anonymous') ? true : false),
            'ANONYMOUS' => $forms_language->get('forms', 'anonymous'),
            'SUBMIT_AS_ANONYMOUS' => $forms_language->get('forms', 'submit_as_anonymous'),
            'SEND_NOTIFY_EMAIL' => $forms_language->get('forms', 'send_notify_email'),
            'CAN_SEND_EMAIL' => $can_view_own,
            'TOKEN' => Token::get()
        ));
        
        $template->addCSSFiles(array(
            (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.css' => array()
        ));

        $template->addJSFiles(array(
            (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.js' => array()
        ));

        $template->addJSScript('
            var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));

            elems.forEach(function(html) {
                var switchery = new Switchery(html, {color: \'#23923d\', secondaryColor: \'#e56464\'});
            });
        ');
        
        $template_file = 'forms/submissions_view.tpl';
    } else {
        switch($_GET['action']) {
            case 'delete_submission':
                // Delete Submission
                if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
                    Redirect::to(URL::build('/panel/forms/submissions'));
                    die();
                }
                
                if($forms->canDeleteSubmission($group_ids, $_GET['id'])){
                    try {
                        $queries->delete('forms_replies', array('id', '=', $_GET['id']));
                        $queries->delete('forms_replies_fields', array('submission_id', '=', $_GET['id']));
                        $queries->delete('forms_comments', array('form_id', '=', $_GET['id']));
                    } catch(Exception $e){
                        die($e->getMessage());
                    }
                }
                
                Redirect::to(URL::build('/panel/forms/submissions'));
                die();
            break;
            case 'delete_comment':
                // Delete comment
                if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
                    Redirect::to(URL::build('/panel/forms/submissions'));
                    die();
                }
                
                if($user->hasPermission('forms.delete-submissions')){
                    try {
                        $queries->delete('forms_comments', array('id', '=', $_GET['id']));
                    } catch(Exception $e){
                        die($e->getMessage());
                    }
                }
                
                Redirect::to(URL::build('/panel/forms/submissions/', 'view=' . Output::getClean($_GET['view'])));
                die();
            break;
            default:
                Redirect::to(URL::build('/panel/forms/submissions'));
                die();
            break;
        }
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets, $template);

if(Session::exists('submission_success'))
    $success = Session::flash('submission_success');

if(isset($success))
    $smarty->assign(array(
        'SUCCESS' => $success,
        'SUCCESS_TITLE' => $language->get('general', 'success')
    ));

if(isset($errors) && count($errors))
    $smarty->assign(array(
        'ERRORS' => $errors,
        'ERRORS_TITLE' => $language->get('general', 'error')
    ));

$smarty->assign(array(
    'PARENT_PAGE' => PARENT_PAGE,
    'PAGE' => PANEL_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'FORMS' => $forms_language->get('forms', 'forms'),
    'SUBMISSIONS' => $forms_language->get('forms', 'submissions'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);
