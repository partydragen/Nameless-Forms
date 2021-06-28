<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr10
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
        /*$submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE status_id IN (SELECT id FROM nl2_forms_statuses WHERE open = 1) AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC')->results();
        $url = URL::build('/panel/forms/submissions/', '');*/
        
        $url_parameters = true;
        $where .= ' AND status_id IN (SELECT id FROM nl2_forms_statuses WHERE open = 1)';
    } else {
        /*if(isset($_GET['form']) && isset($_GET['status'])){
            // Sort by form and status
            $submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE form_id = ? AND status_id = ? AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC', array($_GET['form'], $_GET['status']))->results();
            $url = URL::build('/panel/forms/submissions/',  (isset($_GET['form']) ? 'form=' . $_GET['form'] : '') . '&' . (isset($_GET['status']) ? 'status=' . $_GET['status'] : '') . '&');
        } else if(isset($_GET['form'])) {
            // sort by form
            $submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE form_id = ? AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC', array($_GET['form']))->results();
            $url = URL::build('/panel/forms/submissions/',  (isset($_GET['form']) ? 'form=' . $_GET['form'] : '') . '&');
        } else if(isset($_GET['status'])) {
            // sort by status
            $submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE status_id = ? AND form_id IN (SELECT form_id FROM nl2_forms_permissions WHERE view = 1 AND group_id IN('.$group_ids.')) ORDER BY created DESC', array($_GET['status']))->results();

            $url = URL::build('/panel/forms/submissions/',  (isset($_GET['status']) ? 'status=' . $_GET['status'] : '') . '&');
        } else {
            Redirect::to(URL::build('/panel/forms/submissions/'));
        }*/
        
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
            if($submission->updated_by == null){
                $updated_by_name = $forms_language->get('forms', 'guest');
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
        $submission = $queries->getWhere('forms_replies', array('id', '=', $_GET['view']));
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
        
        // Get form from id
        $form = $queries->getWhere('forms', array('id', '=', $submission->form_id));
        $form = $form[0];
        
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
                    
                    $status = $queries->getWhere('forms_statuses', array('id', '=', $_POST['status']));
                    if(count($status)){
                        $groups = explode(',', $status[0]->gids);
                        $hasperm = false;
                        foreach ($user_groups as $group_id) {
                            if(in_array($group_id, $groups)) {
                                $hasperm = true;
                                break;
                            }
                        }
                        
                        if($hasperm) {
                            $status = $_POST['status'];
                                
                            if($submission->status_id != $_POST['status']) {
                                $any_changes = true;
                            }
                        } else {
                            $status = $submission->status_id;
                        }
                    } else
                        $status = $submission->status_id;
                    
                    if(!empty(Input::get('content'))) {
                        $any_changes = true;
                        $queries->create('forms_comments', array(
                            'form_id' => $submission->id,
                            'user_id' => $user->data()->id,
                            'created' => date('U'),
                            'content' => Output::getClean(nl2br(Input::get('content')))
                        ));
                    }

                    // Was there any changes?
                    if($any_changes == true) {
                        $queries->update('forms_replies', $submission->id, array(
                            'updated_by' => $user->data()->id,
                            'updated' => date('U'),
                            'status_id' => $status
                        ));
                        
                        // Alert user?
                        if($submission->user_id != null) {
                            $target_user = new User($submission->user_id);
                            if($target_user && $forms->canViewOwnSubmission(implode(',', $user->getAllGroupIds()), $submission->form_id)) {
                                Alert::create(
                                    $submission->user_id,
                                    'submission_update',
                                    array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($form->title))),
                                    array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($form->title))),
                                    URL::build('/user/submissions/', 'view=' . Output::getClean($submission->id))
                                );
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
        $answers = json_decode($submission->content, true);
        foreach($answers as $answer){
            $question = $queries->getWhere('forms_fields', array('id', '=', $answer[0]));
            $answer_array[] = array(
                'question' => $question[0]->name,
                'answer' => Output::getPurified(Output::getDecoded($answer[1]))
            );
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
                'content' => Output::getPurified(Output::getDecoded($comment->content)),
                'date' => date('d M Y, H:i', $comment->created),
                'date_friendly' => $timeago->inWords(date('Y-m-d H:i:s', $comment->created), $language->getTimeLanguage()),
                'delete_link' => ($user->hasPermission('forms.delete-submissions') ? URL::build('/panel/forms/submissions/', 'view='.Output::getClean($submission->id).'&action=delete_comment&id=' . Output::getClean($comment->id)) : null)
            );
        }
        
        // Get comments
        $currentstatus = $queries->getWhere('forms_statuses', array('id', '=', $submission->status_id));
        
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
                        'active' => (($currentstatus[0]->id == $status->id) ? true : false),
                        'html' => $status->html,
                        'permission' => $perms
                    );
                }
            }
        }

        $smarty->assign(array(
            'FORM_X' => str_replace('{x}', Output::getClean($form->title), $forms_language->get('forms', 'form_x')),
            'CURRENT_STATUS_X' => str_replace('{x}', $currentstatus[0]->html, $forms_language->get('forms', 'current_status_x')),
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
            'TOKEN' => Token::get()
        ));
        
        $template_file = 'forms/submissions_view.tpl';
    } else {
        switch($_GET['action']) {
            case 'delete_submission':
                // Delete Submission
                if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
                    Redirect::to(URL::build('/panel/forms/submissions'));
                    die();
                }
                
                if($forms->canDeleteSubmission($group_ids, $submission->form_id)){
                    try {
                        $queries->delete('forms_replies', array('id', '=', $_GET['id']));
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
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

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
