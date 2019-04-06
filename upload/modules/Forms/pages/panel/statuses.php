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
		if(!$user->hasPermission('forms.manage')){
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
define('PANEL_PAGE', 'forms');
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if(!isset($_GET['action'])){
	// incase if i want to split statuses later
	Redirect::to(URL::build('/panel/forms'));
	die();
} else {
	switch($_GET['action']){
		case 'new':
			// Deal with input
			if(Input::exists()){
				// Check token
				if(Token::check(Input::get('token'))){
					// Valid token
					// Validate input
					$validate = new Validate();

					$validation = $validate->check($_POST, array(
						'status_html' => array(
							'required' => true,
							'min' => 1,
							'max' => 1024
						)
					));

					if($validation->passed()){
						// Create string containing selected forms IDs
						$forms_string = '';
						if(isset($_POST['status_forms']) && count($_POST['status_forms'])){
							// Turn array of inputted forms into string of forms
							foreach($_POST['status_forms'] as $item){
								$forms_string .= $item . ',';
							}
						}

						$forms_string = rtrim($forms_string, ',');

						$group_string = '';
						if(isset($_POST['status_groups']) && count($_POST['status_groups'])){
							foreach($_POST['status_groups'] as $item){
								$group_string .= $item . ',';
							}
						}

						$group_string = rtrim($group_string, ',');
						
						// is status marked as open
						if(isset($_POST['open']) && $_POST['open'] == 'on') $open = 1;
						else $open = 0;

						try {
							$queries->create('forms_statuses', array(
								'html' => Input::get('status_html'),
								'open' => $open,
								'fids' => $forms_string,
								'gids' => $group_string
							));

							Session::flash('staff_forms', $forms_language->get('forms', 'status_creation_success'));
							Redirect::to(URL::build('/panel/forms'));
							die();
						} catch(Exception $e){
							$errors = array($e->getMessage());
						}

					} else {
						// Validation errors
						$errors = array($forms_language->get('forms', 'status_creation_error'));
					}

				} else {
					// Invalid token
					$errors = array($language->get('general', 'invalid_token'));
				}
			}
		
			// Get a list of forms
			$forms_list = $queries->getWhere('forms', array('id', '<>', 0));
			$template_forms = array();

			if(count($forms_list)){
				foreach($forms_list as $item){
					$template_forms[] = array(
						'id' => Output::getClean($item->id),
						'name' => Output::getClean(Output::getDecoded($item->title))
					);
				}
			}

			// Get a list of all groups
			$group_list = $queries->getWhere('groups', array('id', '<>', 0));
			$template_groups = array();

			if(count($group_list)){
				foreach($group_list as $item){
					$template_groups[] = array(
						'id' => Output::getClean($item->id),
						'name' => Output::getClean(Output::getDecoded($item->name))
					);
				}
			}
		
			$smarty->assign(array(
				'CREATING_STATUS' => $forms_language->get('forms', 'creating_status'),
				'CANCEL' => $language->get('general', 'cancel'),
				'CANCEL_LINK' => URL::build('/panel/forms'),
				'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
				'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
				'YES' => $language->get('general', 'yes'),
				'NO' => $language->get('general', 'no'),
				'STATUS_HTML' => $forms_language->get('forms', 'status_html'),
				'STATUS_FORMS' => $forms_language->get('forms', 'status_forms'),
				'ALL_FORMS' => $template_forms,
				'STATUS_GROUPS' => $forms_language->get('forms', 'status_groups'),
				'ALL_GROUPS' => $template_groups,
				'MARKED_AS_OPEN' => $forms_language->get('forms', 'marked_as_open'),
			));
		
			$template_file = 'forms/status_new.tpl';
		break;
		case 'edit':
			// Editing a status
			if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
				// Check the status ID is valid
				Redirect::to(URL::build('/panel/forms'));
				die();
			}

			// Does the status exist?
			$status = $queries->getWhere('forms_statuses', array('id', '=', $_GET['id']));
			if(!count($status)){
				// No, it doesn't exist
				Redirect::to(URL::build('/panel/forms'));
				die();
			} else {
				$status = $status[0];
			}

			// Deal with input
			if(Input::exists()){
				// Check token
				if(Token::check(Input::get('token'))){
					// Valid token
					// Validate input
					$validate = new Validate();

					$validation = $validate->check($_POST, array(
						'status_html' => array(
							'required' => true,
							'min' => 1,
							'max' => 1024
						)
					));

					if($validation->passed()){
						// Create string containing selected forms IDs
						$forms_string = '';
						if(isset($_POST['status_forms']) && count($_POST['status_forms'])){
							foreach($_POST['status_forms'] as $item){
								// Turn array of inputted forms into string of forms
								$forms_string .= $item . ',';
							}
						}

						$forms_string = rtrim($forms_string, ',');

						$group_string = '';
						if(isset($_POST['status_groups']) && count($_POST['status_groups'])){
							foreach($_POST['status_groups'] as $item){
								$group_string .= $item . ',';
							}
						}

						$group_string = rtrim($group_string, ',');
						
						// is status marked as open
						if(isset($_POST['open']) && $_POST['open'] == 'on') $open = 1;
						else $open = 0;

						try {
							$queries->update('forms_statuses', $status->id, array(
								'html' => Input::get('status_html'),
								'open' => $open,
								'fids' => $forms_string,
								'gids' => $group_string
							));

							Session::flash('staff_forms', $forms_language->get('forms', 'status_edit_success'));
							Redirect::to(URL::build('/panel/forms'));
							die();
						} catch(Exception $e){
							$errors = array($e->getMessage());
						}

					} else {
						// Validation errors
						$errors = array($forms_language->get('forms', 'status_creation_error'));
					}

				} else {
					// Invalid token
					$errors = array($language->get('general', 'invalid_token'));
				}
			}

			// Get a list of forms
			$forms_list = $queries->getWhere('forms', array('id', '<>', 0));
			$template_forms = array();

			// Get a list of forms in which the status is enabled
			$enabled_forms = explode(',', $status->fids);

			if(count($forms_list)){
				foreach($forms_list as $item){
					$template_forms[] = array(
						'id' => Output::getClean($item->id),
						'name' => Output::getClean(Output::getDecoded($item->title)),
						'selected' => (in_array($item->id, $enabled_forms))
					);
				}
			}

			// Get a list of all groups
			$group_list = $queries->getWhere('groups', array('id', '<>', 0));
			$template_groups = array();

			// Get a list of groups which have access to the status
			$groups = explode(',', $status->gids);

			if(count($group_list)){
				foreach($group_list as $item){
					$template_groups[] = array(
						'id' => Output::getClean($item->id),
						'name' => Output::getClean(Output::getDecoded($item->name)),
						'selected' => (in_array($item->id, $groups))
					);
				}
			}
			
			$smarty->assign(array(
				'EDITING_STATUS' => $forms_language->get('forms', 'editing_status'),
				'CANCEL' => $language->get('general', 'cancel'),
				'CANCEL_LINK' => URL::build('/panel/forms'),
				'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
				'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
				'YES' => $language->get('general', 'yes'),
				'NO' => $language->get('general', 'no'),
				'STATUS_HTML' => $forms_language->get('forms', 'status_html'),
				'STATUS_HTML_VALUE' => Output::getClean($status->html),
				'STATUS_FORMS' => $forms_language->get('forms', 'status_forms'),
				'ALL_FORMS' => $template_forms,
				'STATUS_GROUPS' => $forms_language->get('forms', 'status_groups'),
				'ALL_GROUPS' => $template_groups,
				'MARKED_AS_OPEN' => $forms_language->get('forms', 'marked_as_open'),
				'MARKED_AS_OPEN_VALUE' => Output::getClean($status->open),
			));
		
			$template_file = 'forms/status_edit.tpl';
		break;
		case 'delete':
			// status deletion
			if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
				// Check the status ID is valid
				Redirect::to(URL::build('/panel/forms'));
				die();
			}
			
			if($_GET['id'] == 1 || $_GET['id'] == 2) {
				// Check the status ID is valid
				Redirect::to(URL::build('/panel/forms'));
				die();
			}

			$queries->update('forms_statuses', $_GET['id'], array(
				'deleted' => 1
			));
			Session::flash('staff_forms', $forms_language->get('forms', 'status_deleted_successfully'));

			Redirect::to(URL::build('/panel/forms'));
			die();
		break;
		default:
			Redirect::to(URL::build('/panel/forms'));
			die();
		break;
	}
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(Session::exists('forms_statuses'))
	$success = Session::flash('forms_statuses');

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
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'FORMS' => $forms_language->get('forms', 'forms'),
	'STATUSES' => $forms_language->get('forms', 'statuses'),
	'PAGE' => PANEL_PAGE,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit')
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

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);