<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
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
$fields = DB::getInstance()->query('SELECT * FROM nl2_forms_fields WHERE form_id = ? AND deleted = 0 ORDER BY `order`', array($form->id))->results();

// Use recaptcha?
$captcha_enabled = $form->captcha ? true : false;
if ($captcha_enabled) {
    $captcha_type = $queries->getWhere('settings', array('name', '=', 'recaptcha_type'));
    $captcha_type = $captcha_type[0]->value;

    $recaptcha_key = $queries->getWhere("settings", array("name", "=", "recaptcha_key"));
    $recaptcha_secret = $queries->getWhere('settings', array('name', '=', 'recaptcha_secret'));
}

// Handle input
if(Input::exists()){
	if(Token::check(Input::get('token'))){
        $errors = array();
        
        if ($captcha_enabled) {
            // Check reCAPCTHA
            $url = $captcha_type === 'hCaptcha' ? 'https://hcaptcha.com/siteverify' : 'https://www.google.com/recaptcha/api/siteverify';

            $post_data = 'secret=' . $recaptcha_secret[0]->value . '&response=' . ($captcha_type === 'hCaptcha' ? Input::get('h-captcha-response') : Input::get('g-recaptcha-response'));

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            $result = json_decode($result, true);
        } else {
            // reCAPTCHA is disabled
            $result = array(
                'success' => 'true'
            );
        }
        
        // Check if reCAPCTHA was success
        if (isset($result['success']) && $result['success'] == 'true') {
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
                        if(is_numeric($key)) {
                            $content[] = array($key, Output::getClean(nl2br($item)));
                        }
                    }
                    $content = json_encode($content);
                    
                    // Get user id if logged in
                    $user_id = $user->isLoggedIn() ? $user->data()->id : null;

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
                    
                    HookHandler::executeEvent('newFormSubmission', array(
                        'event' => 'newFormSubmission',
                        'username' => Output::getClean($form->title),
                        'content' => str_replace(array('{x}', '{y}'), array($form->title, Output::getClean(($user->isLoggedIn() ? $user->data()->nickname : $forms_language->get('forms', 'guest')))), $forms_language->get('forms', 'new_submission_text')),
                        'content_full' => '',
                        'avatar_url' => ($user->isLoggedIn() ? $user->getAvatar(null, 128, true) : null),
                        'title' => Output::getClean($form->title),
                        'url' => rtrim(Util::getSelfURL(), '/') . URL::build('/panel/forms/submissions/', 'view=' . $submission_id)
                    ));

                    if($form->can_view == 1 && $user->isLoggedIn()) {
                        Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                        Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission_id)));
                        die();
                    } else {
                        Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                        Redirect::to(URL::build($form->url));
                        die();
                    }
                                                
                } catch (Exception $e) {
                   $errors[] = $e->getMessage();
                }
            } else {
                // Validation errors
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
            // reCAPTCHA failed
            $errors[] = $language->get('user', 'invalid_recaptcha');
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
        'value' => (isset($_POST[$field->id]) ? Output::getClean(Input::get($field->id)) : ''),
		'type' => Output::getClean($field->type),
		'required' => Output::getClean($field->required),
		'options' => $options,
	);
}

if ($captcha_enabled) {
    $smarty->assign(array(
        'RECAPTCHA' => Output::getClean($recaptcha_key[0]->value),
        'CAPTCHA_CLASS' => $captcha_type === 'hCaptcha' ? 'h-captcha' : 'g-recaptcha'
    ));

    if ($captcha_type === 'hCaptcha') {
        $template->addJSFiles(
            array(
                'https://hcaptcha.com/1/api.js' => array()
            )
        );
    } else {
        $template->addJSFiles(
            array(
                'https://www.google.com/recaptcha/api.js' => array()
            )
        );
    }
}

if(!empty($form->content)) {
	$smarty->assign('CONTENT', Output::getPurified(Output::getDecoded($form->content)));
}
	
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
	
$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));
	
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
	
// Display template
$template->displayTemplate('forms/form.tpl', $smarty);