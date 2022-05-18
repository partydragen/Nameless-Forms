<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Form page
 */

// Get form info from URL
$form = new Form(rtrim($route, '/'), 'url');
if (!$form->exists()) {
    require(ROOT_PATH . '/404.php');
    die();
}

$forms = new Forms();

if ($user->isLoggedIn()) {
    $group_ids = [];
    foreach ($user->getGroups() as $group) {
        $group_ids[] = $group->id;
    }
} else {
    $group_ids = [0];
}
$group_ids = implode(',', $group_ids);

// Can guests view?
if (!$forms->canPostSubmission($group_ids, $form->data()->id)) {
    if (!$user->isLoggedIn()) {
        Redirect::to(URL::build('/login/'));
        die();
    } else {
        require(ROOT_PATH . '/403.php');
        die();
    }
}

// Always define page name
define('PAGE', 'form-' . $form->data()->id);
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');
require(ROOT_PATH . '/core/includes/bulletproof/bulletproof.php');

// Check if captcha is enabled
$captcha = $form->data()->captcha ? true : false;
if ($captcha) {
    $captcha = CaptchaBase::isCaptchaEnabled();
}

// Handle input
if (Input::exists()) {
    if (Token::check(Input::get('token'))) {
        $errors = [];
        
        if ($captcha) {
            $captcha_passed = CaptchaBase::getActiveProvider()->validateToken($_POST);
        } else {
            $captcha_passed = true;
        }

        // Check if CAPCTHA was success
        if ($captcha_passed) {
            // Validation
            $validation = $form->validateFields($forms_language, $language);
            if ($validation->passed()) {
                // Validation passed
                try {
                    // Get user id if logged in
                    $user_id = $user->isLoggedIn() ? $user->data()->id : null;

                    // Save to database
                    $queries->create('forms_replies', [
                        'form_id' => $form->data()->id,
                        'user_id' => $user_id,
                        'updated_by' => $user_id,
                        'created' => date('U'),
                        'updated' => date('U'),
                        'content' =>  '',
                        'status_id' => 1
                    ]);
                    $submission_id = $queries->getLastId();
                    
                    if(!is_dir(ROOT_PATH . '/uploads/forms_submissions'))
                        mkdir(ROOT_PATH . '/uploads/forms_submissions');

                    try {
                        // Save field values to database
                        $inserts = [];
                        $field_values = [];
                        foreach ($form->getFields() as $field) {
                            if ($field->type != 10) {
                                // Normal POST value
                                if (isset($_POST[$field->id])) {
                                    $item = $_POST[$field->id];
                                    $inserts[] = '(?,?,?),';
                                    
                                    $value = (!is_array($item) ? nl2br($item) : implode(', ', $item));
                                    
                                    $field_values[] = Output::getClean($submission_id);
                                    $field_values[] = Output::getClean($field->id);
                                    $field_values[] = Output::getClean($value);
                                }
                            } else {
                                // File Uploading
                                if (isset($_FILES[$field->id])) {
                                    $image = new Bulletproof\Image($_FILES[$field->id]);
                                    $image->setSize(1, 2097152); // between 1b and 4mb
                                    $image->setDimension(2000, 2000); // 2k x 2k pixel maximum
                                    $image->setMime(['jpg', 'png', 'jpeg']);
                                    $image->setLocation(join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'forms_submissions']));

                                    if ($image->getSize() != 0) {
                                        $upload = $image->upload();
                                        if ($upload) {
                                            $inserts[] = '(?,?,?),';

                                            $field_values[] = Output::getClean($submission_id);
                                            $field_values[] = Output::getClean($field->id);
                                            $field_values[] = Output::getClean($upload->getName() . '.' . $upload->getMime());
                                        } else {
                                            $errors[] = Output::getClean($field->name) . ': ' . $image["error"];
                                        }
                                    }
                                }
                            }
                        }
                        
                        $query = 'INSERT INTO nl2_forms_replies_fields (submission_id, field_id, value) VALUES ';
                        $query .= implode('', $inserts);
                        DB::getInstance()->createQuery(rtrim($query, ','), $field_values);
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                        $queries->delete('forms_replies', ['id', '=', $submission_id]);
                    }
                    
                    if (!count($errors)) {
                        // Trigger new submission event
                        HookHandler::executeEvent('newFormSubmission', [
                            'event' => 'newFormSubmission',
                            'username' => Output::getClean($form->data()->title),
                            'content' => str_replace(['{x}', '{y}'], [$form->data()->title, Output::getClean(($user->isLoggedIn() ? $user->getDisplayname() : $forms_language->get('forms', 'guest')))], $forms_language->get('forms', 'new_submission_text')),
                            'content_full' => '',
                            'avatar_url' => ($user->isLoggedIn() ? $user->getAvatar(128, true) : null),
                            'title' => Output::getClean($form->data()->title),
                            'url' => rtrim(Util::getSelfURL(), '/') . URL::build('/panel/forms/submissions/', 'view=' . $submission_id)
                        ]);

                        // Redirect to submission view if user have view access, if not redirect back 
                        if ($user->isLoggedIn() && $forms->canViewOwnSubmission($group_ids, $form->data()->id)) {
                            Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                            Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission_id)));
                            die();
                        } else {
                            Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                            Redirect::to(URL::build($form->data()->url));
                            die();
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            } else {
                // Validation errors
                $errors = $validation->errors();
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

$fields_array = [];
foreach ($form->getFields() as $field) {
    $options = explode(',', Output::getClean($field->options));
    $fields_array[] = [
        'id' => Output::getClean($field->id),
        'name' => Output::getClean($field->name),
        'value' => (isset($_POST[$field->id]) && !is_array($_POST[$field->id]) ? Output::getClean(Input::get($field->id)) : ''),
        'type' => Output::getClean($field->type),
        'required' => Output::getClean($field->required),
        'options' => $options,
        'info' => Output::getPurified(Output::getDecoded($field->info))
    ];
}

// Captcha
if ($captcha) {
    $smarty->assign('CAPTCHA', CaptchaBase::getActiveProvider()->getHtml());
    $template->addJSFiles([CaptchaBase::getActiveProvider()->getJavascriptSource() => []]);

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

if (!empty($form->data()->content)) {
    $smarty->assign('CONTENT', Output::getPurified(Output::getDecoded($form->data()->content)));
}
    
$smarty->assign([
    'TITLE' => Output::getClean($form->data()->title),
    'FIELDS' => $fields_array,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->addCSSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/css/spoiler.css' => []
]);

$template->addJSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => []
]);
    
// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $mod_nav], $widgets, $template);

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

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();
  
$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));
    
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
    
// Display template
$template->displayTemplate('forms/form.tpl', $smarty);