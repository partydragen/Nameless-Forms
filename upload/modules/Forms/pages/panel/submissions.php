<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Forms module - panel form page
 */

// Can the user view the panel?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	}
	if(!$user->isAdmLoggedIn()){
		// Needs to authenticate
		Redirect::to(URL::build('/panel/auth'));
		die();
	} else {
		if(!$user->hasPermission('forms.view-submissions')){
			require_once(ROOT_PATH . '/404.php');
			die();
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'forms');
define('PANEL_PAGE', 'submissions');
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

$timeago = new Timeago(TIMEZONE);

if(!isset($_GET['view'])){
	$submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies ORDER BY created DESC')->results();
	
	if(count($submissions_query)){
		// Get page
		if(isset($_GET['p'])){
			if(!is_numeric($_GET['p'])){
				Redirect::to($url);
				die();
			} else {
				if($_GET['p'] == 1){
					// Avoid bug in pagination class
					Redirect::to($url);
					die();
				}
				$p = $_GET['p'];
			}
		} else {
			$p = 1;
		}
		
		$paginator = new Paginator((isset($template_pagination) ? $template_pagination : array()));
		$results = $paginator->getLimited($submissions_query, 10, $p, count($submissions_query));
		$pagination = $paginator->generate(7, $url);
		
		// Get all submissions
		$submissions = array();
		
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
				$user_name = Output::getClean($user->idToNickname($submission->user_id));
				$user_profile = URL::build('/panel/user/' . Output::getClean($submission->user_id . '-' . $user->idToName($submission->user_id)));
				$user_style = $user->getGroupClass($submission->user_id);
				$user_avatar = $user->getAvatar($submission->user_id, '', 128);
			}
			
			// Is user a guest or a user
			if($submission->updated_by == null){
				$updated_by_name = $forms_language->get('forms', 'guest');
				$updated_by_profile = null;
				$updated_by_style = null;
				$updated_by_avatar = null;
			} else {
				$updated_by_name = Output::getClean($user->idToNickname($submission->updated_by));
				$updated_by_profile = URL::build('/panel/user/' . Output::getClean($submission->updated_by . '-' . $user->idToName($submission->updated_by)));
				$updated_by_style = $user->getGroupClass($submission->updated_by);
				$updated_by_avatar = $user->getAvatar($submission->updated_by, '', 128);
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
	
	$smarty->assign(array(
		'SUBMISSIONS_LIST' => $submissions,
		'VIEW' => $language->get('general', 'view'),
		'NO_SUBMISSIONS' => $forms_language->get('forms', 'no_open_submissions'),
		'FORM' => $forms_language->get('forms', 'form'),
		'USER' => $forms_language->get('forms', 'user'),
		'UPDATED_BY' => $forms_language->get('forms', 'updated_by'),
		'STATUS' => $forms_language->get('forms', 'status'),
		'ACTIONS' => $forms_language->get('forms', 'actions'),
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
                        if(in_array($user->data()->group_id, $groups)) {
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
							'content' => Output::getClean(Input::get('content'))
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
						if($form->can_view == 1 && $submission->user_id != null) {
							Alert::create(
								$submission->user_id,
								'submission_update',
								array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($form->title))),
								array('path' => ROOT_PATH . '/modules/Forms/language', 'file' => 'forms', 'term' => 'your_submission_updated', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($form->title))),
								URL::build('/user/submissions/', 'view=' . Output::getClean($submission->id))
							);
						}

						$success = $language->get('moderator', 'comment_created');
						
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
				'answer' => $answer[1]
			);
		}
		
		// Get comments
		$comments = $queries->getWhere('forms_comments', array('form_id', '=', $submission->id));
		$smarty_comments = array();
		foreach($comments as $comment){
			$smarty_comments[] = array(
				'username' => Output::getClean($user->idToNickname($comment->user_id)),
				'profile' => URL::build('/panel/user/' . Output::getClean($comment->user_id . '-' . $user->idToName($comment->user_id))),
				'style' => $user->getGroupClass($comment->user_id),
				'avatar' => $user->getAvatar($comment->user_id),
				'content' => Output::getPurified(Output::getDecoded($comment->content)),
				'date' => date('d M Y, H:i', $comment->created),
				'date_friendly' => $timeago->inWords(date('Y-m-d H:i:s', $comment->created), $language->getTimeLanguage())
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
			$user_name = Output::getClean($user->idToNickname($submission->user_id));
			$user_profile = URL::build('/panel/user/' . Output::getClean($submission->user_id . '-' . $user->idToName($submission->user_id)));
			$user_style = $user->getGroupClass($submission->user_id);
			$user_avatar = $user->getAvatar($submission->user_id, '', 128);
		}
		
		// Form statuses
		$statuses = array();

		$form_statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
		if(count($form_statuses)){
			foreach($form_statuses as $status){
				$form_ids = explode(',', $status->fids);
				
				if(in_array($submission->form_id, $form_ids)){
					// Check permissions
					$groups = explode(',', $status->gids);
					$perms = false;
					if(in_array($user->data()->group_id, $groups)){
						$perms = true;
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
			'STATUSES' => $statuses,
			'TOKEN' => Token::get()
		));
		
		$template_file = 'forms/submissions_view.tpl';
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