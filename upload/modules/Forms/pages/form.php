<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr11
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

require_once(ROOT_PATH . '/modules/Forms/classes/Forms.php');
$forms = new Forms();

if ($user->isLoggedIn()) {
    $group_ids = array();
    foreach ($user->getGroups() as $group) {
        $group_ids[] = $group->id;
    }
} else {
    $group_ids = array(0);
}
$group_ids = implode(',', $group_ids);

// Can guests view?
if(!$forms->canPostSubmission($group_ids, $form->id)){
    if (!$user->isLoggedIn()) {
        Redirect::to(URL::build('/login/'));
        die();
    } else {
        require(ROOT_PATH . '/403.php');
        die();
    }
}

// Always define page name
define('PAGE', 'form-' . $form->id);
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Get fields
$fields = DB::getInstance()->query('SELECT * FROM nl2_forms_fields WHERE form_id = ? AND deleted = 0 ORDER BY `order`', array($form->id))->results();

// Check if captcha is enabled
$captcha = $form->captcha ? true : false;
if ($captcha) {
    $captcha = CaptchaBase::isCaptchaEnabled();
}

// Handle input
if(Input::exists()){
    if(Token::check(Input::get('token'))){
        $errors = array();
        
        if ($captcha) {
            $captcha_passed = CaptchaBase::getActiveProvider()->validateToken($_POST);
        } else {
            $captcha_passed = true;
        }
        
        // Check if CAPCTHA was success
        if ($captcha_passed) {
            // Validation
            $validate = new Validate();
            $to_validate = array();
            
            foreach($fields as $field){
                $field_validation = array();
                
                if($field->required == 1 /*&& $field->type != 9*/) {
                    $field_validation['required'] = true;
                }
                
                if($field->min != 0) {
                    $field_validation['min'] = $field->min;
                }
                
                if($field->max != 0) {
                    $field_validation['max'] = $field->max;
                }
                
                if(count($field_validation)) {
                    $to_validate[$field->id] = $field_validation;
                }
            }
            
            // Modify post validation
            $validate_post = array();
            foreach($_POST as $key => $item){
                $validate_post[$key] = !is_array($item) ? $item : true ;
            }
            
            $validation = $validate->check($validate_post, $to_validate);
            if($validation->passed()){
                // Validation passed
                try {
                    // Get user id if logged in
                    $user_id = $user->isLoggedIn() ? $user->data()->id : null;

                    // Save to database
                    $queries->create('forms_replies', array(
                        'form_id' => $form->id,
                        'user_id' => $user_id,
                        'updated_by' => $user_id,
                        'created' => date('U'),
                        'updated' => date('U'),
                        'content' =>  '',
                        'status_id' => 1
                    ));
                    $submission_id = $queries->getLastId();
                    
                    try {
                        // Save field values to database
                        unset($_POST['token']);
                        $inserts = array();
                        $field_values = array();
                        foreach($_POST as $key => $item){
                            if(is_numeric($key)) {
                                $inserts[] = '(?,?,?),';
                                
                                $value = (!is_array($item) ? nl2br($item) : implode(', ', $item));
                                
                                $field_values[] = Output::getClean($submission_id);
                                $field_values[] = Output::getClean($key);
                                $field_values[] = Output::getClean($value);
                            }
                        }
                        
                        $query = 'INSERT INTO nl2_forms_replies_fields (submission_id, field_id, value) VALUES ';
                        $query .= implode('', $inserts);
                        DB::getInstance()->createQuery(rtrim($query, ','), $field_values);
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                        $queries->delete('forms_replies', array('id', '=', $submission_id));
                    }
                    
                    if(!count($errors)) {
                        // Trigger new submission event
                        HookHandler::executeEvent('newFormSubmission', array(
                            'event' => 'newFormSubmission',
                            'username' => Output::getClean($form->title),
                            'content' => str_replace(array('{x}', '{y}'), array($form->title, Output::getClean(($user->isLoggedIn() ? $user->data()->nickname : $forms_language->get('forms', 'guest')))), $forms_language->get('forms', 'new_submission_text')),
                            'content_full' => '',
                            'avatar_url' => ($user->isLoggedIn() ? $user->getAvatar(128, true) : null),
                            'title' => Output::getClean($form->title),
                            'url' => rtrim(Util::getSelfURL(), '/') . URL::build('/panel/forms/submissions/', 'view=' . $submission_id)
                        ));

                        // Redirect to submission view if user have view access, if not redirect back 
                        if($user->isLoggedIn() && $forms->canViewOwnSubmission($group_ids, $form->id)) {
                            Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                            Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission_id)));
                            die();
                        } else {
                            Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                            Redirect::to(URL::build($form->url));
                            die();
                        }
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
                    if (count($fielderror)) {
                        $fielderror = $fielderror[0];

                        if(strpos($item, 'is required') !== false){
                            $errors[] = str_replace('{x}', Output::getClean($fielderror->name), $language->get('user', 'field_is_required'));
                        } else if(strpos($item, 'minimum') !== false){
                            $errors[] = str_replace(array('{x}', '{y}'), array(Output::getClean($fielderror->name), $fielderror->min), $forms_language->get('forms', 'x_field_minimum_y'));
                        } else if(strpos($item, 'maximum') !== false){
                            $errors[] = str_replace(array('{x}', '{y}'), array(Output::getClean($fielderror->name), $fielderror->max), $forms_language->get('forms', 'x_field_maximum_y'));
                        }
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
        'value' => (isset($_POST[$field->id]) && !is_array($_POST[$field->id]) ? Output::getClean(Input::get($field->id)) : ''),
        'type' => Output::getClean($field->type),
        'required' => Output::getClean($field->required),
        'options' => $options,
        'info' => Output::getPurified(Output::getDecoded($field->info))
    );
}

// Captcha
if ($captcha) {
    $smarty->assign('CAPTCHA', CaptchaBase::getActiveProvider()->getHtml());
    $template->addJSFiles(array(CaptchaBase::getActiveProvider()->getJavascriptSource() => array()));

    $submitScript = CaptchaBase::getActiveProvider()->getJavascriptSubmit('forms');
    if ($submitScript) {
        $template->addJSScript('
            $("#forms").submit(function(e) {
                e.preventDefault();
                ' . $submitScript . '
            });
        ');
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