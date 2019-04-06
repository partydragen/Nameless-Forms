<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Form page
 */
 
// Get form info from URL
$form = $queries->getWhere('forms', array('url', '=', rtrim($route, '/')));
if(!count($form)){
    require(ROOT_PATH . '/404.php');
    die();
} else {
    $form = $form[0];
}

// Can guests view?
if($form->guest == 0 && !$user->isLoggedIn()){
	Redirect::to(URL::build('/login/'));
	die();
}

// Always define page name
define('PAGE', 'form-' . $form->id);
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get fields
//fields = $queries->getWhere('forms_fields', array('form_id', '=', $form->id));
$fields = DB::getInstance()->query('SELECT * FROM nl2_forms_fields WHERE form_id = ? AND deleted = 0 ORDER BY `order`', array($form->id))->results();
	
// Handle input
if(Input::exists()){
	if(Token::check(Input::get('token'))){
		// Validation
		$validate = new Validate();
		$to_validate = array();
		
		foreach($fields as $field){
			if($field->required == 1) {
				$to_validate[$field->id] = array(
					'required' => true
				);
			}
		}
		
		$validation = $validate->check($_POST, $to_validate);
		if($validation->passed()){
			// Validation passed
			try {
				// Convert to content
				$content = array();
				unset($_POST['token']);
				foreach($_POST as $key => $item){
					$content[] = array($key, htmlspecialchars($item));
				}
				$content = json_encode($content);
					
				// Get user id if logged in
				if($user->isLoggedIn()){
					$user_id = $user->data()->id;
				} else {
					$user_id = null;
				}
					
				// Save to database
				$queries->create('forms_replies', array(
					'form_id' => $form->id,
					'user_id' => $user_id,
					'updated_by' => $user_id,
					'created' => date('U'),
					'updated' => date('U'),
					'content' =>  $content,
					'status_id' => 1
				));
				
				$submission_id = $queries->getLastId();

				if($form->can_view == 1 && $user->isLoggedIn()) {
					Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
					Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission_id)));
					die();
				} else {
					$success = $forms_language->get('forms', 'form_submitted');
				}
											
            } catch (Exception $e) {
               $errors[] = $e->getMessage();
            }
		} else {
			// Validation errors
			$errors = array();
			foreach($validation->errors() as $item){
                // Get field name
                $id = explode(' ', $item);
                $id = $id[0];

                $fielderror = $queries->getWhere('forms_fields', array('id', '=', $id));
                if (count($field)) {
                    $fielderror = $fielderror[0];
                    $errors[] = str_replace('{x}', Output::getClean($fielderror->name), $language->get('user', 'field_is_required'));
                }
			}
		}
	} else {
		// Invalid token
		$errors[] = $language->get('general', 'invalid_token');
	}
}

$fields_array = array();
foreach($fields as $field){
	$options = explode(',', Output::getClean($field->options));
	$fields_array[] = array(
		'id' => Output::getClean($field->id),
		'name' => Output::getClean($field->name),
		'type' => Output::getClean($field->type),
		'required' => Output::getClean($field->required),
		'options' => $options,
	);
}

if(isset($errors)) $smarty->assign('ERRORS', $errors);
if(isset($success)) $smarty->assign('SUCCESS', $success);
	
$smarty->assign(array(
	'TITLE' => Output::getClean($form->title),
	'FIELDS' => $fields_array,
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit')
));
	
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

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();
	
$smarty->assign('WIDGETS', $widgets->getWidgets());
	
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
	
// Display template
$template->displayTemplate('forms/form.tpl', $smarty);