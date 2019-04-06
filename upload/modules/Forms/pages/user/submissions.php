<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Forms module - user submission page
 */
 
// Must be logged in
if(!$user->isLoggedIn()){
	Redirect::to(URL::build('/'));
	die();
}

// Always define page name for navbar
define('PAGE', 'cc_submissions');
$page_title = $language->get('user', 'user_cp');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

$timeago = new Timeago(TIMEZONE);

if(!isset($_GET['view'])){
	$submissions_query = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE user_id = ? AND form_id IN (SELECT id FROM nl2_forms WHERE can_view = 1) ORDER BY created DESC', array($user->data()->id))->results();
	
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
			
			$submissions[] = array(
				'id' => $submission->id,
				'form_name' => $form->title,
				'status' => $status->html,
				'created_at' => $timeago->inWords(date('Y-m-d H:i:s', $submission->created), $language->getTimeLanguage()),
				'updated_by_name' => Output::getClean($user->idToNickname($submission->updated_by)),
				'updated_by_profile' => URL::build('/profile/' . Output::getClean($user->idToName($submission->updated_by))),
				'updated_by_style' => $user->getGroupClass($submission->updated_by),
				'updated_by_avatar' => $user->getAvatar($submission->updated_by, '', 128),
				'updated_at' => $timeago->inWords(date('Y-m-d H:i:s', $submission->updated), $language->getTimeLanguage()),
				'link' => URL::build('/user/submissions/', 'view=' . $submission->id),
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
	));
	
	$template_file = 'forms/submissions.tpl';
} else {
	// Get submission by id
	$submission = DB::getInstance()->query('SELECT * FROM nl2_forms_replies WHERE id = ? AND user_id = ? AND form_id IN (SELECT id FROM nl2_forms WHERE can_view = 1) ORDER BY created DESC', array($_GET['view'], $user->data()->id))->results();
	
	if(!count($submission)){
		Redirect::to(URL::build('/user/submissions'));
		die();
	}
	$submission = $submission[0];
	
	// Check input
	if(Input::exists()){
		$errors = array();
		
		// Check token
		if(Token::check(Input::get('token'))){
			// Valid token
			$validate = new Validate();

			$validation = $validate->check($_POST, array(
				'content' => array(
					'required' => true,
					'min' => 3,
					'max' => 10000
				)
			));

			if($validation->passed()){
				$queries->create('forms_comments', array(
					'form_id' => $submission->id,
					'user_id' => $user->data()->id,
					'created' => date('U'),
					'content' => Output::getClean(Input::get('content'))
				));

				$queries->update('forms_replies', $submission->id, array(
					'updated_by' => $user->data()->id,
					'updated' => date('U')
				));

				$success = $language->get('moderator', 'comment_created');
					
				Session::flash('submission_success', $forms_language->get('forms', 'submission_updated'));
				Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission->id)));
				die();
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
			'profile' => URL::build('/profile/' . Output::getClean($user->idToName($comment->user_id))),
			'style' => $user->getGroupClass($comment->user_id),
			'avatar' => $user->getAvatar($comment->user_id),
			'content' => Output::getPurified(Output::getDecoded($comment->content)),
			'date' => date('d M Y, H:i', $comment->created),
			'date_friendly' => $timeago->inWords(date('Y-m-d H:i:s', $comment->created), $language->getTimeLanguage())
		);
	}
		
	$form = $queries->getWhere('forms', array('id', '=', $submission->form_id));
	$form = $form[0];
		
	// Get comments
	$currentstatus = $queries->getWhere('forms_statuses', array('id', '=', $submission->status_id));

	$smarty->assign(array(
		'FORM_X' => str_replace('{x}', $form->title, $forms_language->get('forms', 'form_x')),
		'CURRENT_STATUS_X' => str_replace('{x}', $currentstatus[0]->html, $forms_language->get('forms', 'current_status_x')),
		'LAST_UPDATED' => $forms_language->get('forms', 'last_updated'),
		'LAST_UPDATED_DATE' => date('d M Y, H:i', $submission->updated),
		'LAST_UPDATED_FRIENDLY' => $timeago->inWords(date('Y-m-d H:i:s', $submission->updated), $language->getTimeLanguage()),
		'USER' => Output::getClean($user->idToNickname($submission->user_id)),
		'USER_PROFILE' => URL::build('/profile/' . Output::getClean($user->idToName($submission->user_id))),
		'USER_STYLE' => $user->getGroupClass($submission->user_id),
		'USER_AVATAR' => $user->getAvatar($submission->user_id, '', 128),
		'CREATED_DATE' => date('d M Y, H:i', $submission->created),
		'CREATED_DATE_FRIENDLY' => $timeago->inWords(date('Y-m-d H:i:s', $submission->created), $language->getTimeLanguage()),
		'COMMENTS' => $smarty_comments,
		'COMMENTS_TEXT' => $language->get('moderator', 'comments'),
		'NEW_COMMENT' => $language->get('moderator', 'new_comment'),
		'NO_COMMENTS' => $language->get('moderator', 'no_comments'),
		'ANSWERS' => $answer_array,
		'TOKEN' => Token::get(),
		'SUBMIT' => $language->get('general', 'submit')
	));
	
	$template_file = 'forms/submissions_view.tpl';
}

// Language values
$smarty->assign(array(
	'USER_CP' => $language->get('user', 'user_cp'),
	'SUBMISSIONS' => $forms_language->get('forms', 'submissions'),
));


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

require(ROOT_PATH . '/core/templates/cc_navbar.php');

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate($template_file, $smarty);