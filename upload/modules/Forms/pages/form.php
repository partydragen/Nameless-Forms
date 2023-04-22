<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.1
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

// Forum require user to be logged in
if ($form->data()->source == 'forum' && !$user->isLoggedIn()) {
    Redirect::to(URL::build('/login/'));
}

// Can guests view?
if (!$forms->canPostSubmission($group_ids, $form->data()->id)) {
    if (!$user->isLoggedIn()) {
        Redirect::to(URL::build('/login/'));
    } else {
        require(ROOT_PATH . '/403.php');
        die();
    }
}

// Always define page name
define('PAGE', 'form-' . $form->data()->id);
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

// Execute event with allow modules to interact with it
$renderFormEvent = EventHandler::executeEvent('renderForm', [
    'user' => $user,
    'form' => $form,
    'content' => $form->data()->content,
    'fields' => $form->getFields()
]);

// Check if the event returned any errors
if (isset($renderFormEvent['errors']) && count($renderFormEvent['errors'])) {
    Session::flash('home_error', $renderFormEvent['errors'][0]);
    Redirect::to(URL::build('/'));
}

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
            $validation = $form->validateFields($_POST, $forms_language, $language);

            if ($validation->passed()) {
                // Validation passed
                $submission = new Submission();

                if ($submission->create($form, $user, $_POST)) {
                    // Redirect to submission view if user have view access, if not redirect back 
                    if ($user->isLoggedIn() && $forms->canViewOwnSubmission($group_ids, $form->data()->id)) {
                        // Check if submission is submitted to different source
                        if ($submission->data()->source != null) {
                            $source = Forms::getInstance()->getSubmissionSource($submission->data()->source);
                            if ($source != null) {
                                Redirect::to($source->getURL($submission));
                            }
                        }

                        Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                        Redirect::to(URL::build('/user/submissions/', 'view=' . Output::getClean($submission->data()->id)));

                    } else {
                        Session::flash('submission_success', $forms_language->get('forms', 'form_submitted'));
                        Redirect::to(URL::build($form->data()->url));
                    }
                } else {
                    $errors = $submission->getErrors();
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
foreach ($renderFormEvent['fields'] as $field) {
    $options = explode(',', Output::getClean(str_replace("\r" , "", $field->options)));
    $fields_array[] = [
        'id' => Output::getClean($field->id),
        'name' => Output::getClean($field->name),
        'value' =>  isset($_POST[$field->id]) ? is_array(Input::get($field->id)) ? Input::get($field->id) : Output::getClean(Input::get($field->id)) : Output::getClean($field->default_value),
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

if (!empty($renderFormEvent['content'])) {
    $smarty->assign('CONTENT', $renderFormEvent['content']);
}

$smarty->assign([
    'TITLE' => Output::getClean($form->data()->title),
    'FIELDS' => $fields_array,
    'TOKEN' => Token::get(),
    'CHOOSE_FILE' => $forms_language->get('forms', 'choose_picture'),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->assets()->include([
    AssetTree::TINYMCE,
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

$template->onPageLoad();
  
$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));
    
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
    
// Display template
$template->displayTemplate('forms/form.tpl', $smarty);