<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
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
    // incase if i want to split statuses later
    Redirect::to(URL::build('/panel/forms'));
    die();
} else {
    switch ($_GET['action']) {
        case 'new':
            // Deal with input
            if (Input::exists()) {
                // Check token
                if (Token::check(Input::get('token'))) {
                    // Valid token
                    // Validate input
                    $validate = new Validate();

                    $validation = $validate->check($_POST, [
                        'status_html' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 1024
                        ]
                    ])->messages([
                        'status_html' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'status_creation_error'),
                            Validate::MIN => $forms_language->get('forms', 'status_creation_error'),
                            Validate::MAX => $forms_language->get('forms', 'status_creation_error')
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Create string containing selected forms IDs
                        $forms_string = '';
                        if (isset($_POST['status_forms']) && count($_POST['status_forms'])) {
                            // Turn array of inputted forms into string of forms
                            foreach ($_POST['status_forms'] as $item) {
                                $forms_string .= $item . ',';
                            }
                        }

                        $forms_string = rtrim($forms_string, ',');

                        $group_string = '';
                        if (isset($_POST['status_groups']) && count($_POST['status_groups'])) {
                            foreach ($_POST['status_groups'] as $item) {
                                $group_string .= $item . ',';
                            }
                        }

                        $group_string = rtrim($group_string, ',');
                        
                        // is status marked as open
                        if (isset($_POST['open']) && $_POST['open'] == 'on') $open = 1;
                        else $open = 0;

                        try {
                            $queries->create('forms_statuses', [
                                'html' => Input::get('status_html'),
                                'open' => $open,
                                'fids' => $forms_string,
                                'gids' => $group_string
                            ]);

                            Session::flash('staff_forms', $forms_language->get('forms', 'status_creation_success'));
                            Redirect::to(URL::build('/panel/forms'));
                            die();
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                    } else {
                        // Validation errors
                        $errors = $validation->errors();
                    }

                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
        
            // Get a list of forms
            $forms_list = $queries->getWhere('forms', ['id', '<>', 0]);
            $template_forms = [];

            if (count($forms_list)) {
                foreach ($forms_list as $item) {
                    $template_forms[] = [
                        'id' => Output::getClean($item->id),
                        'name' => Output::getClean(Output::getDecoded($item->title))
                    ];
                }
            }

            // Get a list of all groups
            $group_list = $queries->getWhere('groups', ['id', '<>', 0]);
            $template_groups = [];

            if (count($group_list)) {
                foreach ($group_list as $item) {
                    $template_groups[] = [
                        'id' => Output::getClean($item->id),
                        'name' => Output::getClean(Output::getDecoded($item->name))
                    ];
                }
            }
        
            $smarty->assign([
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
            ]);
        
            $template_file = 'forms/status_new.tpl';
        break;
        case 'edit':
            // Editing a status
            if (!isset($_GET['status']) || !is_numeric($_GET['status'])) {
                // Check the status ID is valid
                Redirect::to(URL::build('/panel/forms'));
                die();
            }

            $status = new Status($_GET['status']);
            if (!$status->exists()) {
                // No, it doesn't exist
                Redirect::to(URL::build('/panel/forms'));
                die();
            }

            // Deal with input
            if (Input::exists()) {
                // Check token
                if (Token::check(Input::get('token'))) {
                    // Valid token
                    // Validate input
                    $validate = new Validate();

                    $validation = $validate->check($_POST, [
                        'status_html' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 1,
                            Validate::MAX => 1024
                        ]
                    ])->messages([
                        'status_html' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'status_creation_error'),
                            Validate::MIN => $forms_language->get('forms', 'status_creation_error'),
                            Validate::MAX => $forms_language->get('forms', 'status_creation_error')
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Create string containing selected forms IDs
                        $forms_string = '';
                        if (isset($_POST['status_forms']) && count($_POST['status_forms'])) {
                            foreach ($_POST['status_forms'] as $item) {
                                // Turn array of inputted forms into string of forms
                                $forms_string .= $item . ',';
                            }
                        }

                        $forms_string = rtrim($forms_string, ',');

                        $group_string = '';
                        if (isset($_POST['status_groups']) && count($_POST['status_groups'])) {
                            foreach ($_POST['status_groups'] as $item) {
                                $group_string .= $item . ',';
                            }
                        }

                        $group_string = rtrim($group_string, ',');
                        
                        // is status marked as open
                        if (isset($_POST['open']) && $_POST['open'] == 'on') $open = 1;
                        else $open = 0;

                        try {
                            $status->update([
                                'html' => Input::get('status_html'),
                                'open' => $open,
                                'fids' => $forms_string,
                                'gids' => $group_string
                            ]);

                            Session::flash('staff_forms', $forms_language->get('forms', 'status_edit_success'));
                            Redirect::to(URL::build('/panel/forms'));
                            die();
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                    } else {
                        // Validation errors
                        $errors = $validation->errors();
                    }

                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            // Get a list of forms
            $forms_list = $queries->getWhere('forms', ['id', '<>', 0]);
            $template_forms = [];

            // Get a list of forms in which the status is enabled
            $enabled_forms = explode(',', $status->data()->fids);

            if (count($forms_list)) {
                foreach ($forms_list as $item) {
                    $template_forms[] = [
                        'id' => Output::getClean($item->id),
                        'name' => Output::getClean(Output::getDecoded($item->title)),
                        'selected' => (in_array($item->id, $enabled_forms))
                    ];
                }
            }

            // Get a list of all groups
            $group_list = $queries->getWhere('groups', ['id', '<>', 0]);
            $template_groups = [];

            // Get a list of groups which have access to the status
            $groups = explode(',', $status->data()->gids);

            if (count($group_list)) {
                foreach ($group_list as $item) {
                    $template_groups[] = [
                        'id' => Output::getClean($item->id),
                        'name' => Output::getClean(Output::getDecoded($item->name)),
                        'selected' => (in_array($item->id, $groups))
                    ];
                }
            }
            
            $smarty->assign([
                'EDITING_STATUS' => $forms_language->get('forms', 'editing_status'),
                'CANCEL' => $language->get('general', 'cancel'),
                'CANCEL_LINK' => URL::build('/panel/forms'),
                'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
                'CONFIRM_CANCEL' => $language->get('general', 'confirm_cancel'),
                'YES' => $language->get('general', 'yes'),
                'NO' => $language->get('general', 'no'),
                'STATUS_HTML' => $forms_language->get('forms', 'status_html'),
                'STATUS_HTML_VALUE' => Output::getClean($status->data()->html),
                'STATUS_FORMS' => $forms_language->get('forms', 'status_forms'),
                'ALL_FORMS' => $template_forms,
                'STATUS_GROUPS' => $forms_language->get('forms', 'status_groups'),
                'ALL_GROUPS' => $template_groups,
                'MARKED_AS_OPEN' => $forms_language->get('forms', 'marked_as_open'),
                'MARKED_AS_OPEN_VALUE' => Output::getClean($status->data()->open),
            ]);
        
            $template_file = 'forms/status_edit.tpl';
        break;
        case 'delete':
            // status deletion
            if (!isset($_GET['status']) || !is_numeric($_GET['status'])) {
                // Check the status ID is valid
                Redirect::to(URL::build('/panel/forms'));
                die();
            }
            
            $status = new Status($_GET['status']);
            if ($status->exists() && $status->data()->id != 1 && $status->data()->id != 2) {
                $status->delete();
                Session::flash('staff_forms', $forms_language->get('forms', 'status_deleted_successfully'));
            }

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
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $mod_nav], $widgets, $template);

if (Session::exists('forms_statuses'))
    $success = Session::flash('forms_statuses');

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
    'DASHBOARD' => $language->get('admin', 'dashboard'),
    'FORMS' => $forms_language->get('forms', 'forms'),
    'STATUSES' => $forms_language->get('forms', 'statuses'),
    'PAGE' => PANEL_PAGE,
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit')
]);

$template->addCSSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.css' => []
]);

$template->addJSFiles([
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/switchery/switchery.min.js' => []
]);

$template->addJSScript('
    var elems = Array.prototype.slice.call(document.querySelectorAll(\'.js-switch\'));

    elems.forEach(function(html) {
        var switchery = new Switchery(html, {color: \'#23923d\', secondaryColor: \'#e56464\'});
    });
');

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();
$smarty->assign(Forms::pdp($cache));

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);