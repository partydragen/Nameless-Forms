<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.1
 *
 *  License: MIT
 *
 *  Forms module - panel form page
 */

// Can the user view the panel?
if (!$user->handlePanelPageLoad('forms.manage')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'forms');
define('PANEL_PAGE', 'forms');
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/backend_init.php');
require_once(ROOT_PATH . '/modules/Forms/classes/Forms.php');

if (!isset($_GET['action'])) {

    // Get forms from database
    $forms = DB::getInstance()->orderAll('forms', 'id', 'ASC')->results();
    $forms_array = [];
    if (count($forms)) {
        foreach ($forms as $form) {
            $forms_array[] = [
                'name' => Output::getClean($form->title),
                'edit_link' => URL::build('/panel/form/', 'form=' . Output::getClean($form->id)),
                'delete_link' => URL::build('/panel/forms/', 'action=delete&form=' . Output::getClean($form->id))
            ];
        }
    }

    // Get statuses from database
    $statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
    $status_array = [];
    if (count($statuses)) {
        foreach ($statuses as $status) {
            $status_array[] = [
                'id' => $status->id,
                'html' => Output::getPurified($status->html),
                'open' => $status->open,
                'edit_link' => URL::build('/panel/forms/statuses', 'action=edit&status=' . Output::getClean($status->id)),
                'delete_link' => URL::build('/panel/forms/statuses', 'action=delete&status=' . Output::getClean($status->id))
            ];
        }
    }

    $smarty->assign([
        'FORM' => $forms_language->get('forms', 'form'),
        'NEW_FORM' => $forms_language->get('forms', 'new_form'),
        'NEW_FORM_LINK' => URL::build('/panel/forms/', 'action=new'),
        'FORMS_LIST' => $forms_array,
        'NEW_STATUS' => $forms_language->get('forms', 'new_status'),
        'NEW_STATUS_LINK' => URL::build('/panel/forms/statuses/', 'action=new'),
        'STATUS' => $forms_language->get('forms', 'status'),
        'STATUSES' => $forms_language->get('forms', 'statuses'),
        'MARKED_AS_OPEN' => $forms_language->get('forms', 'marked_as_open'),
        'STATUS_LIST' => $status_array,
        'NONE_FORMS_DEFINED' => $forms_language->get('forms', 'none_forms_defined'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_FORM' => $forms_language->get('forms', 'delete_form'),
        'CONFIRM_DELETE_STATUS' => $forms_language->get('forms', 'delete_status'),
        'ACTION' => $forms_language->get('forms', 'action'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no')
        
    ]);

    $template_file = 'forms/forms.tpl';
} else {
    switch ($_GET['action']) {
        case 'new':
            // New Form
            if (Input::exists()) {
                $errors = [];
                if (Token::check(Input::get('token'))) {
                    // Validate input
                    $validation = Validate::check($_POST, [
                        'form_name' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 32
                        ],
                        'form_url' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 32
                        ],
                        'form_icon' => [
                            Validate::MAX => 64
                        ]
                    ])->messages([
                        'form_name' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'input_form_name'),
                            Validate::MIN => $forms_language->get('forms', 'form_name_minimum'),
                            Validate::MAX => $forms_language->get('forms', 'form_name_maximum')
                        ],
                        'form_url' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'input_form_url'),
                            Validate::MIN => $forms_language->get('forms', 'form_url_minimum'),
                            Validate::MAX => $forms_language->get('forms', 'form_url_maximum')
                        ],
                        'form_icon' => [
                            Validate::MAX => $forms_language->get('forms', 'form_icon_maximum')
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Create form
                        try {
                            if (strpos(Input::get('form_url'), '/') === 0) {
                                // Get link location
                                if (isset($_POST['link_location'])) {
                                    switch ($_POST['link_location']) {
                                        case 1:
                                        case 2:
                                        case 3:
                                        case 4:
                                            $location = $_POST['link_location'];
                                            break;
                                        default:
                                        $location = 1;
                                    }
                                } else
                                $location = 1;

                                // Enable captcha?
                                if (isset($_POST['captcha']) && $_POST['captcha'] == 'on') $captcha = 1;
                                else $captcha = 0;
                                        
                                // Save to database
                                DB::getInstance()->insert('forms', [
                                    'url' => rtrim(Input::get('form_url'), '/'),
                                    'title' => Input::get('form_name'),
                                    'link_location' => $location,
                                    'icon' => Input::get('form_icon'),
                                    'captcha' => $captcha,
                                    'content' => Input::get('content')
                                ]);
                                Session::flash('staff_forms', $forms_language->get('forms', 'form_created_successfully'));
                                Redirect::to(URL::build('/panel/forms'));
                            } else {
                                $errors[] = $forms_language->get('forms', 'form_url_slash');
                            }
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        // Validation Errors
                        $errors = $validation->errors();
                    }
                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $smarty->assign([
                'CREATING_NEW_FORM' => $forms_language->get('forms', 'creating_new_form'),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/forms'),
                'FORM_NAME' => $forms_language->get('forms', 'form_name'),
                'FORM_NAME_VALUE' => (isset($_POST['form_name']) ? Output::getClean(Input::get('form_name')) : ''),
                'FORM_ICON' => $forms_language->get('forms', 'form_icon'),
                'FORM_ICON_VALUE' => (isset($_POST['form_icon']) ? Input::get('form_icon') : ''),
                'FORM_URL' => $forms_language->get('forms', 'form_url'),
                'FORM_URL_VALUE' => (isset($_POST['form_url']) ? Output::getClean(Input::get('form_url')) : ''),
                'FORM_LINK_LOCATION' => $forms_language->get('forms', 'link_location'),
                'LINK_LOCATION_VALUE' => (isset($_POST['link_location']) ? Output::getClean(Input::get('link_location')) : ''),
                'LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
                'LINK_MORE' => $language->get('admin', 'page_link_more'),
                'LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
                'LINK_NONE' => $language->get('admin', 'page_link_none'),
                'CONTENT' => $language->get('admin', 'description'),
                'CONTENT_VALUE' => (isset($_POST['content']) ? Output::getClean(Input::get('content')) : ''),
                'ENABLE_CAPTCHA' => $forms_language->get('forms', 'enable_captcha'),
                'ENABLE_CAPTCHA_VALUE' => (isset($_POST['captcha']) && $_POST['captcha'] == 'on' ? 1 : 0),
            ]);

            $template->assets()->include([
                AssetTree::TINYMCE,
            ]);

            $template->addJSScript(Input::createTinyEditor($language, 'inputContent', null, false, true));

            $template_file = 'forms/forms_new.tpl';
        break;
        case 'delete':
            // Delete Form
            if (!isset($_GET['form']) || !is_numeric($_GET['form'])) {
                Redirect::to(URL::build('/panel/forms'));
            }

            $form = new Form($_GET['form']);
            if ($form->exists()) {
                $form->delete();
                Session::flash('staff_forms', $forms_language->get('forms', 'form_deleted_successfully'));
            }

            Redirect::to(URL::build('/panel/forms'));
        break;
        default:
            Redirect::to(URL::build('/panel/forms'));
        break;
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

if (Session::exists('staff_forms'))
    $success = Session::flash('staff_forms');

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

$smarty->assign([
    'PARENT_PAGE' => PARENT_PAGE,
    'PAGE' => PANEL_PAGE,
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'INFO' => $language->get('general', 'info'),
    'FORMS' => $forms_language->get('forms', 'forms'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);