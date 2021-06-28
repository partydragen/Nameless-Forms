<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr10
 *
 *  License: MIT
 *
 *  Forms module - panel form page
 */

// Can the user view the panel?
if(!$user->handlePanelPageLoad('forms.manage')) {
    require_once(ROOT_PATH . '/403.php');
    die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'forms');
define('PANEL_PAGE', 'forms');
$page_title = $forms_language->get('forms', 'forms');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

if(!is_numeric($_GET['form'])){
    Redirect::to(URL::build('/panel/forms'));
    die();
} else {
    $form = $queries->getWhere('forms', array('id', '=', $_GET['form']));
    if(!count($form)){
        Redirect::to(URL::build('/panel/forms'));
        die();
    }
}
$form = $form[0];

$field_types = array();
$field_types[1] = array('id' => 1, 'name' => $language->get('admin', 'text'));
$field_types[2] = array('id' => 2, 'name' => $forms_language->get('forms', 'options'));
$field_types[3] = array('id' => 3, 'name' => $language->get('admin', 'textarea'));
$field_types[4] = array('id' => 4, 'name' => $forms_language->get('forms', 'help_box'));
$field_types[5] = array('id' => 5, 'name' => $forms_language->get('forms', 'barrier'));
$field_types[6] = array('id' => 6, 'name' => $forms_language->get('forms', 'number'));
$field_types[7] = array('id' => 7, 'name' => $language->get('general', 'email_address'));

if(!isset($_GET['action'])){
    // Editing form
    if(Input::exists()){
        $errors = array();
        if(Token::check(Input::get('token'))){
            // Validate input
            $validate = new Validate();
            $validation = $validate->check($_POST, array(
                'form_name' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 32
                ),
                'form_url' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 32
                ),
                'form_icon' => array(
                    'max' => 64
                )
            ));
                                
            if($validation->passed()){
                // Update form
                try {
                    // Get link location
                    if(isset($_POST['link_location'])){
                        switch($_POST['link_location']){
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
                    if(isset($_POST['captcha']) && $_POST['captcha'] == 'on') $captcha = 1;
                    else $captcha = 0;
                                    
                    // Save to database
                    $queries->update('forms', $form->id, array(
                        'url' => Output::getClean(rtrim(Input::get('form_url'), '/')),
                        'title' => Output::getClean(Input::get('form_name')),
                        'link_location' => $location,
                        'icon' => Input::get('form_icon'),
                        'captcha' => $captcha,
                        'content' => Output::getClean(Input::get('content'))
                    ));
                                        
                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form=' . Output::getClean($form->id)));
                    die();
                } catch(Exception $e){
                    $errors[] = $e->getMessage();
                }
            } else {
                // Errors
                foreach($validation->errors() as $item){
                    if(strpos($item, 'is required') !== false){
                        switch($item){
                            case (strpos($item, 'form_name') !== false):
                                $errors[] = $forms_language->get('forms', 'input_form_name');
                            break;
                            case (strpos($item, 'form_url') !== false):
                                $errors[] = $forms_language->get('forms', 'input_form_url');
                            break;
                        }
                    } else if(strpos($item, 'minimum') !== false){
                        switch($item){
                            case (strpos($item, 'form_name') !== false):
                                $errors[] = $forms_language->get('forms', 'form_name_minimum');
                            break;
                            case (strpos($item, 'form_url') !== false):
                                $errors[] = $forms_language->get('forms', 'form_url_minimum');
                            break;
                        }
                    } else if(strpos($item, 'maximum') !== false){
                        switch($item){
                            case (strpos($item, 'form_name') !== false):
                                $errors[] = $forms_language->get('forms', 'form_name_maximum');
                            break;
                            case (strpos($item, 'form_url') !== false):
                                $errors[] = $forms_language->get('forms', 'form_url_maximum');
                            break;
                            case (strpos($item, 'form_icon') !== false):
                                $errors[] = $forms_language->get('forms', 'form_icon_maximum');
                            break;
                        }
                    }
                }
            }
        } else {
            // Invalid token
            $errors[] = $language->get('general', 'invalid_token');
        }
    }
    
    // Get form fields from database
    $fields = DB::getInstance()->query('SELECT * FROM nl2_forms_fields WHERE form_id = ? AND deleted = 0 ORDER BY `order`', array($form->id))->results();
    $fields_array = array();
    if(count($fields)){
        foreach($fields as $field){
            $fields_array[] = array(
                'name' => Output::getClean($field->name),
                'type' => $field_types[$field->type]['name'],
                'edit_link' => URL::build('/panel/form/', 'form='.$form->id .'&amp;action=edit&id='.$field->id),
                'delete_link' => URL::build('/panel/form/', 'form='.$form->id .'&amp;action=delete&amp;id=' . $field->id)
            );
        }
    }

    $smarty->assign(array(
        'EDITING_FORM' => str_replace('{x}', Output::getClean($form->title), $forms_language->get('forms', 'editing_x')),
        'BACK' => $language->get('general', 'back'),
        'BACK_LINK' => URL::build('/panel/forms'),
        'FORM_NAME' => $forms_language->get('forms', 'form_name'),
        'FORM_NAME_VALUE' => Output::getClean(htmlspecialchars_decode($form->title)),
        'FORM_ICON' => $forms_language->get('forms', 'form_icon'),
        'FORM_ICON_VALUE' => Output::getClean(htmlspecialchars_decode($form->icon)),
        'FORM_URL' => $forms_language->get('forms', 'form_url'),
        'FORM_URL_VALUE' => Output::getClean(htmlspecialchars_decode($form->url)),
        'FORM_LINK_LOCATION' => $forms_language->get('forms', 'link_location'),
        'LINK_LOCATION_VALUE' => $form->link_location,
        'LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
        'LINK_MORE' => $language->get('admin', 'page_link_more'),
        'LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
        'LINK_NONE' => $language->get('admin', 'page_link_none'),
        'CONTENT' => $language->get('admin', 'description'),
        'CONTENT_VALUE' => (isset($_POST['content']) ? Output::getClean(Input::get('content')) : Output::getClean(Output::getDecoded($form->content))),
        'ENABLE_CAPTCHA' => $forms_language->get('forms', 'enable_captcha'),
        'ENABLE_CAPTCHA_VALUE' => $form->captcha,
        'FIELDS' => $forms_language->get('forms', 'fields'),
        'NEW_FIELD' => $forms_language->get('forms', 'new_field'),
        'NEW_FIELD_LINK' => URL::build('/panel/form/', 'form='.$form->id.'&amp;action=new'),
        'FIELDS_LIST' => $fields_array,
        'NONE_FIELDS_DEFINED' => $forms_language->get('forms', 'none_fields_defined'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_FIELD' => $forms_language->get('forms', 'delete_field'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no')
    ));
    
    $template->addCSSFiles(array(
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/css/spoiler.css' => array()
    ));

    $template->addJSFiles(array(
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js' => array(),
        (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/ckeditor/ckeditor.js' => array()
    ));

    $template->addJSScript(Input::createEditor('inputContent', true));
    
    $template_file = 'forms/form.tpl';
} else {
    switch($_GET['action']){
        case 'new':
            // New Field
            if(Input::exists()){
                $errors = array();
                if(Token::check(Input::get('token'))){
                    // Validate input
                    $validate = new Validate();
                    $validation = $validate->check($_POST, array(
                        'field_name' => array(
                            'required' => true,
                            'min' => 2,
                            'max' => 255
                        )
                    ));
                                        
                    if($validation->passed()){
                        // Create field
                        try {
                            // Get field type
                            $type = 1;
                            if(array_key_exists($_POST['type'], $field_types)) {
                                $type = $_POST['type'];
                            }
                                                
                            // Is this field required
                            if(isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
                            else $required = 0;
                                                
                            // Get options into a string
                            $options = str_replace("\n", ',', Input::get('options'));
                                            
                            // Save to database
                            $queries->create('forms_fields', array(
                                'form_id' => $_GET['form'],
                                'name' => Output::getClean(Input::get('field_name')),
                                'type' => $type,
                                'required' => $required,
                                'options' => htmlspecialchars($options),
                                'order' => Input::get('order')
                            ));
                                    
                            Session::flash('staff_forms', $forms_language->get('forms', 'field_created_successfully'));
                            Redirect::to(URL::build('/panel/form/', 'form=' . $form->id));
                            die();
                        } catch(Exception $e){
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        // Errors
                        foreach($validation->errors() as $item){
                            if(strpos($item, 'is required') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'input_field_name');
                                    break;
                                }
                            } else if(strpos($item, 'minimum') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'field_name_minimum');
                                    break;
                                }
                            } else if(strpos($item, 'maximum') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'field_name_maximum');
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
        
            $smarty->assign(array(
                'NEW_FIELD_FOR_X' => str_replace('{x}', Output::getClean($form->title), $forms_language->get('forms', 'new_field_for_x')),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/form/', 'form=' . Output::getClean($form->id)),
                'FIELD_NAME' => $language->get('admin', 'field_name'),
                'TYPE' => $language->get('admin', 'type'),
                'TYPES' =>  $field_types,
                'OPTIONS' => $forms_language->get('forms', 'options'),
                'OPTIONS_HELP' => $forms_language->get('forms', 'options_help'),
                'FIELD_ORDER' => $forms_language->get('forms', 'field_order'),
                'REQUIRED' => $language->get('admin', 'required'),
            ));
        
            $template_file = 'forms/field_new.tpl';
        break;
        case 'edit':
            if(!is_numeric($_GET['id'])){
                Redirect::to(URL::build('/panel/forms'));
                die();
            } else {
                $field = $queries->getWhere('forms_fields', array('id', '=', $_GET['id']));
                if(!count($field)){
                    Redirect::to(URL::build('/panel/forms'));
                    die();
                }
            }
            $field = $field[0];

            // Edit Field
            if(Input::exists()){
                $errors = array();
                if(Token::check(Input::get('token'))){
                    // Validate input
                    $validate = new Validate();
                    $validation = $validate->check($_POST, array(
                        'field_name' => array(
                            'required' => true,
                            'min' => 2,
                            'max' => 255
                        )
                    ));
                                        
                    if($validation->passed()){
                        // Create field
                        try {
                            // Get field type
                            $type = 1;
                            if(array_key_exists($_POST['type'], $field_types)) {
                                $type = $_POST['type'];
                            }
                                                
                            // Is this field required
                            if(isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
                            else $required = 0;
                                                
                            // Get options into a string
                            $options = str_replace("\n", ',', Input::get('options'));
                                            
                            // Save to database
                            $queries->update('forms_fields', $field->id, array(
                                'name' => Output::getClean(Input::get('field_name')),
                                'type' => $type,
                                'required' => $required,
                                'options' => htmlspecialchars($options),
                                '`order`' => Input::get('order')
                            ));
                                    
                            Session::flash('staff_forms', $forms_language->get('forms', 'field_updated_successfully'));
                            Redirect::to(URL::build('/panel/form/', 'form=' . $form->id));
                            die();
                        } catch(Exception $e){
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        // Errors
                        foreach($validation->errors() as $item){
                            if(strpos($item, 'is required') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'input_field_name');
                                    break;
                                }
                            } else if(strpos($item, 'minimum') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'field_name_minimum');
                                    break;
                                }
                            } else if(strpos($item, 'maximum') !== false){
                                switch($item){
                                    case (strpos($item, 'field_name') !== false):
                                        $errors[] = $forms_language->get('forms', 'field_name_maximum');
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
            
             // Get already inputted options
            if($field->options == null){
                $options = '';
            } else {
                $options = str_replace(',', "\n", htmlspecialchars($field->options));
            }
        
            $smarty->assign(array(
                'EDITING_FIELD_FOR_X' => str_replace('{x}', Output::getClean($form->title), $forms_language->get('forms', 'editing_field_for_x')),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/form/', 'form=' . Output::getClean($form->id)),
                'FIELD_NAME' => $language->get('admin', 'field_name'),
                'FIELD_NAME_VALUE' => Output::getClean($field->name),
                'TYPE' => $language->get('admin', 'type'),
                'TYPE_VALUE' => $field->type,
                'TYPES' => $field_types,
                'OPTIONS' => $forms_language->get('forms', 'options'),
                'OPTIONS_HELP' => $forms_language->get('forms', 'options_help'),
                'OPTIONS_VALUE' => $options,
                'FIELD_ORDER' => $forms_language->get('forms', 'field_order'),
                'ORDER_VALUE' => $field->order,
                'REQUIRED' => $language->get('admin', 'required'),
                'REQUIRED_VALUE' => $field->required,
            ));
        
            $template_file = 'forms/field.tpl';
        break;
        case 'delete':
            // Delete Field
            if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
                Redirect::to(URL::build('/panel/forms'));
                die();
            }
            $queries->update('forms_fields', $_GET['id'], array(
                'deleted' => 1
            ));
                
            Session::flash('staff_forms', $forms_language->get('forms', 'field_deleted_successfully'));
            Redirect::to(URL::build('/panel/form/', 'form='.$form->id));
            die();
        break;
        case 'permissions':
            // Form permissions
            if(Input::exists()){
                $errors = array();
                
                if(Token::check(Input::get('token'))){
                    // Display navigation link for guest?
                    if(isset($_POST['guest']) && $_POST['guest'] == 'on') $guest = 1;
                    else $guest = 0;
                    
                    // Save to database
                    $queries->update('forms', $form->id, array(
                        'guest' => $guest
                    ));
                    
                    // Update form permissions
                    $post = Input::get('perm-post-0');
                    $view_own = Input::get('perm-view_own-0');
                    $view_submissions = 0;
                    $delete_submissions = 0;
                    
                    $groups = DB::getInstance()->query('SELECT id FROM nl2_groups')->results();
                    $form_perm_query = $queries->getWhere('forms_permissions', array('form_id', '=', $form->id));
                    
                    $cat_perm_exists = 0;
                    if(count($form_perm_query)){
                        foreach($form_perm_query as $query){
                            if($query->group_id == 0){
                                $cat_perm_exists = 1;
                                $update_id = $query->id;
                                break;
                            }
                        }
                    }
                    
                    try {
                        if($cat_perm_exists != 0){ // Permission already exists, update
                            // Update the category
                            $queries->update('forms_permissions', $update_id, array(
                                'post' => $post,
                                'view_own' => $view_own,
                                'view' => $view_submissions,
                                'can_delete' => $delete_submissions
                            ));
                        } else {
                            // Permission doesn't exist, create
                            $queries->create('forms_permissions', array(
                                'group_id' => 0,
                                'form_id' => $form->id,
                                'post' => $post,
                                'view_own' => $view_own,
                                'view' => $view_submissions,
                                'can_delete' => $delete_submissions
                            ));
                        }
                    } catch(Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                    
                    foreach($groups as $group){
                        $post = Input::get('perm-post-' . $group->id);
                        $view_own = Input::get('perm-view_own-' . $group->id);
                        $view_submissions = Input::get('perm-view_submissions-' . $group->id);
                        $delete_submissions = Input::get('perm-delete_submissions-' . $group->id);
                        
                        if(!($post)) $post = 0;
                        if(!($view_own)) $view_own = 0;
                        if(!($view_submissions)) $view_submissions = 0;
                        if(!($delete_submissions)) $delete_submissions = 0;
                        
                        $cat_perm_exists = 0;
                        if(count($form_perm_query)){
                            foreach($form_perm_query as $query){
                                if($query->group_id == $group->id){
                                    $cat_perm_exists = 1;
                                    $update_id = $query->id;
                                    break;
                                }
                            }
                        }
                        
                        try {
                            if($cat_perm_exists != 0){
                                // Permission already exists, update
                                $queries->update('forms_permissions', $update_id, array(
                                    'post' => $post,
                                    'view_own' => $view_own,
                                    'view' => $view_submissions,
                                    'can_delete' => $delete_submissions
                                ));
                            } else {
                                // Permission doesn't exist, create
                                $queries->create('forms_permissions', array(
                                    'group_id' => $group->id,
                                    'form_id' => $form->id,
                                    'post' => $post,
                                    'view_own' => $view_own,
                                    'view' => $view_submissions,
                                    'can_delete' => $delete_submissions
                                ));
                            }
                        } catch(Exception $e) {
                            die($e->getMessage());
                        }
                    }
                    
                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form='.$form->id.'&action=permissions'));
                    die();
                } else
                    $errors[] = $language->get('general', 'invalid_token');
            }
            
            $guest_query = DB::getInstance()->query('SELECT 0 AS id, post AS can_post, view_own AS can_view_own FROM nl2_forms_permissions WHERE group_id = 0 AND form_id = ?', array($form->id))->results();
            $group_query = DB::getInstance()->query('SELECT id, name, can_post, can_view_own, can_view, can_delete FROM nl2_groups A LEFT JOIN (SELECT group_id, post AS can_post, `view_own` AS can_view_own, `view` AS can_view, can_delete FROM nl2_forms_permissions WHERE form_id = ?) B ON A.id = B.group_id ORDER BY `order` ASC', array($form->id))->results();
        
            $smarty->assign(array(
                'EDITING_FORM' => str_replace('{x}', Output::getClean($form->title), $forms_language->get('forms', 'editing_x')),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/forms'),
                'USER' => $language->get('admin', 'user'),
                'STAFFCP' => $language->get('moderator', 'staff_cp'),
                'GROUP' => $language->get('admin', 'group'),
                'GUESTS' => $language->get('user', 'guests'),
                'GUEST_PERMISSIONS' => $guest_query,
                'GROUP_PERMISSIONS' => $group_query,
                'CAN_POST_SUBMISSION' => $forms_language->get('forms', 'can_post_submission'),
                'CAN_VIEW_OWN_SUBMISSION' => $forms_language->get('forms', 'can_view_own_submission'),
                'CAN_VIEW_SUBMISSIONS' => $forms_language->get('forms', 'can_view_submissions'),
                'CAN_DELETE_SUBMISSIONS' => $forms_language->get('forms', 'can_delete_submissions'),
                'SHOW_NAVIGATION_LINK_FOR_GUEST' => $forms_language->get('forms', 'show_navigation_link_for_guest'),
            ));
            
            $template_file = 'forms/form_permissions.tpl';
        break;
        default:
            Redirect::to(URL::build('/panel/forms'));
            die();
        break;
    }
}

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

if(Session::exists('staff_forms'))
    $success = Session::flash('staff_forms');

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
    'INFO' => $language->get('general', 'info'),
    'FORMS' => $forms_language->get('forms', 'forms'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'GENERAL_SETTINGS' => $language->get('admin', 'general_settings'),
    'GENERAL_SETTINGS_LINK' => URL::build('/panel/form/', 'form='.$form->id),
    'PERMISSIONS' => $language->get('admin', 'permissions'),
    'PERMISSIONS_LINK' => URL::build('/panel/form/', 'form='.$form->id.'&amp;action=permissions'),
    'GUEST_VALUE' => $form->guest
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