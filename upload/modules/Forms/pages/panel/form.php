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

if (!is_numeric($_GET['form'])) {
    Redirect::to(URL::build('/panel/forms'));
}

$form = new Form($_GET['form']);
if (!$form->exists()) {
    Redirect::to(URL::build('/panel/forms'));
}

$field_types = [];
$field_types[1] = ['id' => 1, 'name' => $language->get('admin', 'text')];
$field_types[2] = ['id' => 2, 'name' => $forms_language->get('forms', 'options')];
$field_types[3] = ['id' => 3, 'name' => $language->get('admin', 'textarea')];
$field_types[4] = ['id' => 4, 'name' => $forms_language->get('forms', 'help_box')];
$field_types[5] = ['id' => 5, 'name' => $forms_language->get('forms', 'barrier')];
$field_types[6] = ['id' => 6, 'name' => $forms_language->get('forms', 'number')];
$field_types[7] = ['id' => 7, 'name' => $language->get('user', 'email_address')];
$field_types[8] = ['id' => 8, 'name' => $forms_language->get('forms', 'radio')];
$field_types[9] = ['id' => 9, 'name' => $forms_language->get('forms', 'checkbox')];
$field_types[10] = ['id' => 10, 'name' => $forms_language->get('forms', 'file')];

if (!isset($_GET['action'])) {
    // Editing form
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
                // Update form
                try {
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
                    $form->update([
                        'url' => rtrim(Input::get('form_url'), '/'),
                        'title' => Input::get('form_name'),
                        'link_location' => $location,
                        'icon' => Input::get('form_icon'),
                        'captcha' => $captcha,
                        'content' => Input::get('content')
                    ]);

                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form=' . Output::getClean($form->data()->id)));
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

    // Get form fields from database
    $fields_array = [];
    foreach ($form->getFields() as $field) {
        $fields_array[] = [
            'name' => Output::getClean($field->name),
            'type' => $field_types[$field->type]['name'],
            'edit_link' => URL::build('/panel/form/', 'form='.$form->data()->id .'&amp;action=edit&id='.$field->id),
            'delete_link' => URL::build('/panel/form/', 'form='.$form->data()->id .'&amp;action=delete&amp;id=' . $field->id)
        ];
    }

    $smarty->assign([
        'FORM_NAME' => $forms_language->get('forms', 'form_name'),
        'FORM_NAME_VALUE' => Output::getClean(htmlspecialchars_decode($form->data()->title)),
        'FORM_ICON' => $forms_language->get('forms', 'form_icon'),
        'FORM_ICON_VALUE' => Output::getClean(htmlspecialchars_decode($form->data()->icon)),
        'FORM_URL' => $forms_language->get('forms', 'form_url'),
        'FORM_URL_VALUE' => Output::getClean(htmlspecialchars_decode($form->data()->url)),
        'FORM_LINK_LOCATION' => $forms_language->get('forms', 'link_location'),
        'LINK_LOCATION_VALUE' => $form->data()->link_location,
        'LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
        'LINK_MORE' => $language->get('admin', 'page_link_more'),
        'LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
        'LINK_NONE' => $language->get('admin', 'page_link_none'),
        'CONTENT' => $language->get('admin', 'description'),
        'CONTENT_VALUE' => (isset($_POST['content']) ? Output::getClean(Input::get('content')) : Output::getClean(Output::getDecoded($form->data()->content))),
        'ENABLE_CAPTCHA' => $forms_language->get('forms', 'enable_captcha'),
        'ENABLE_CAPTCHA_VALUE' => $form->data()->captcha,
        'NEW_FIELD' => $forms_language->get('forms', 'new_field'),
        'NEW_FIELD_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&amp;action=new'),
        'FIELDS_LIST' => $fields_array,
        'NONE_FIELDS_DEFINED' => $forms_language->get('forms', 'none_fields_defined'),
        'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
        'CONFIRM_DELETE_FIELD' => $forms_language->get('forms', 'delete_field'),
        'YES' => $language->get('general', 'yes'),
        'NO' => $language->get('general', 'no')
    ]);

    $template->assets()->include([
        AssetTree::TINYMCE,
    ]);

    $template->addJSScript(Input::createTinyEditor($language, 'inputContent', null, false, true));
    
    $template_file = 'forms/form.tpl';
} else {
    switch($_GET['action']) {
        case 'new':
            // New Field
            if (Input::exists()) {
                $errors = [];
                if (Token::check(Input::get('token'))) {
                    // Validate input
                    $validation = Validate::check($_POST, [
                        'field_name' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ]
                    ])->messages([
                        'field_name' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'input_field_name'),
                            Validate::MIN => $forms_language->get('forms', 'field_name_minimum'),
                            Validate::MAX => $forms_language->get('forms', 'field_name_maximum')
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Create field
                        try {
                            // Get field type
                            $type = 1;
                            if (array_key_exists($_POST['type'], $field_types)) {
                                $type = $_POST['type'];
                            }

                            // Is this field required
                            if (isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
                            else $required = 0;

                            // Get options into a string
                            $options = str_replace("\n", ',', Input::get('options'));

                            // Save to database
                            DB::getInstance()->insert('forms_fields', [
                                'form_id' => $_GET['form'],
                                'name' => Output::getClean(Input::get('field_name')),
                                'type' => $type,
                                'required' => $required,
                                'options' => htmlspecialchars($options),
                                'info' => Output::getClean(nl2br(Input::get('info'))),
                                'order' => Input::get('order'),
                                'min' => Input::get('minimum'),
                                'max' => Input::get('maximum')
                            ]);
                                    
                            Session::flash('staff_forms', $forms_language->get('forms', 'field_created_successfully'));
                            Redirect::to(URL::build('/panel/form/', 'form=' . $form->data()->id));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        // Validation Errors
                        $errors = $validation->errors();
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
        
            $smarty->assign([
                'NEW_FIELD_FOR_X' => $forms_language->get('forms', 'new_field_for_x', ['form' => Output::getClean($form->data()->title)]),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/form/', 'form=' . Output::getClean($form->data()->id)),
                'FIELD_NAME' => $language->get('admin', 'field_name'),
                'TYPE' => $language->get('admin', 'type'),
                'TYPES' =>  $field_types,
                'OPTIONS' => $forms_language->get('forms', 'options'),
                'OPTIONS_HELP' => $forms_language->get('forms', 'options_help'),
                'CHECKBOX' => $forms_language->get('forms', 'checkbox'),
                'RADIO' => $forms_language->get('forms', 'radio'),
                'FIELD_ORDER' => $forms_language->get('forms', 'field_order'),
                'MINIMUM_CHARACTERS' => $forms_language->get('forms', 'minimum_characters'),
                'MAXIMUM_CHARACTERS' => $forms_language->get('forms', 'maximum_characters'),
                'REQUIRED' => $language->get('admin', 'required'),
            ]);
        
            $template_file = 'forms/field_new.tpl';
        break;
        case 'edit':
            if (!is_numeric($_GET['id'])) {
                Redirect::to(URL::build('/panel/forms'));
            } else {
                $field = DB::getInstance()->get('forms_fields', ['id', '=', $_GET['id']])->results();
                if (!count($field)) {
                    Redirect::to(URL::build('/panel/forms'));
                }
            }
            $field = $field[0];

            // Edit Field
            if (Input::exists()) {
                $errors = [];
                if (Token::check(Input::get('token'))) {
                    // Validate input
                    $validation = Validate::check($_POST, [
                        'field_name' => [
                            Validate::REQUIRED => true,
                            Validate::MIN => 2,
                            Validate::MAX => 255
                        ]
                    ])->messages([
                        'field_name' => [
                            Validate::REQUIRED => $forms_language->get('forms', 'input_field_name'),
                            Validate::MIN => $forms_language->get('forms', 'field_name_minimum'),
                            Validate::MAX => $forms_language->get('forms', 'field_name_maximum')
                        ]
                    ]);

                    if ($validation->passed()) {
                        // Create field
                        try {
                            // Get field type
                            $type = 1;
                            if (array_key_exists($_POST['type'], $field_types)) {
                                $type = $_POST['type'];
                            }

                            // Is this field required
                            if (isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
                            else $required = 0;

                            // Get options into a string
                            $options = str_replace("\n", ',', Input::get('options'));

                            // Save to database
                            DB::getInstance()->update('forms_fields', $field->id, [
                                'name' => Input::get('field_name'),
                                'type' => $type,
                                'required' => $required,
                                'options' => htmlspecialchars($options),
                                'info' => nl2br(Input::get('info')),
                                'min' => Input::get('minimum'),
                                'max' => Input::get('maximum'),
                                'order' => Input::get('order')
                            ]);
                                    
                            Session::flash('staff_forms', $forms_language->get('forms', 'field_updated_successfully'));
                            Redirect::to(URL::build('/panel/form/', 'form=' . $form->data()->id));
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    } else {
                        // Validation Errors
                        $errors = $validation->errors();
                    }
                } else {
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }
            
             // Get already inputted options
            if ($field->options == null) {
                $options = '';
            } else {
                $options = str_replace(',', "\n", htmlspecialchars($field->options));
            }
        
            $smarty->assign([
                'EDITING_FIELD_FOR_X' => $forms_language->get('forms', 'editing_field_for_x', ['form' => Output::getClean($form->data()->title)]),
                'BACK' => $language->get('general', 'back'),
                'BACK_LINK' => URL::build('/panel/form/', 'form=' . Output::getClean($form->data()->id)),
                'FIELD_NAME' => $language->get('admin', 'field_name'),
                'FIELD_NAME_VALUE' => Output::getClean($field->name),
                'TYPE' => $language->get('admin', 'type'),
                'TYPE_VALUE' => $field->type,
                'TYPES' => $field_types,
                'OPTIONS' => $forms_language->get('forms', 'options'),
                'OPTIONS_HELP' => $forms_language->get('forms', 'options_help'),
                'OPTIONS_VALUE' => $options,
                'CHECKBOX' => $forms_language->get('forms', 'checkbox'),
                'RADIO' => $forms_language->get('forms', 'radio'),
                'INFO_VALUE' => Output::getClean($field->info),
                'FIELD_ORDER' => $forms_language->get('forms', 'field_order'),
                'ORDER_VALUE' => $field->order,
                'MINIMUM_CHARACTERS' => $forms_language->get('forms', 'minimum_characters'),
                'MINIMUM_CHARACTERS_VALUE' => $field->min,
                'MAXIMUM_CHARACTERS' => $forms_language->get('forms', 'maximum_characters'),
                'MAXIMUM_CHARACTERS_VALUE' => $field->max,
                'REQUIRED' => $language->get('admin', 'required'),
                'REQUIRED_VALUE' => $field->required,
            ]);
        
            $template_file = 'forms/field.tpl';
        break;
        case 'delete':
            // Delete Field
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                Redirect::to(URL::build('/panel/forms'));
            }
            DB::getInstance()->update('forms_fields', $_GET['id'], [
                'deleted' => 1
            ]);
                
            Session::flash('staff_forms', $forms_language->get('forms', 'field_deleted_successfully'));
            Redirect::to(URL::build('/panel/form/', 'form='.$form->data()->id));
        break;
        case 'fields':
            // Get form fields from database
            $fields_array = [];
            foreach ($form->getFields() as $field) {
                $fields_array[] = [
                    'name' => Output::getClean($field->name),
                    'order' => Output::getClean($field->order),
                    'type' => $field_types[$field->type]['name'],
                    'required' => Output::getClean($field->required),
                    'edit_link' => URL::build('/panel/form/', 'form='.$form->data()->id .'&amp;action=edit&id='.$field->id),
                    'delete_link' => URL::build('/panel/form/', 'form='.$form->data()->id .'&amp;action=delete&amp;id=' . $field->id)
                ];
            }
            
            $smarty->assign([
                'FIELD_NAME' => $language->get('admin', 'field_name'),
                'ORDER' => $forms_language->get('forms', 'field_order'),
                'TYPE' => $language->get('admin', 'type'),
                'REQUIRED' => $language->get('admin', 'required'),
                'ACTIONS' => $language->get('general', 'actions'),
                'NEW_FIELD' => $forms_language->get('forms', 'new_field'),
                'NEW_FIELD_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&amp;action=new'),
                'FIELDS_LIST' => $fields_array,
                'NONE_FIELDS_DEFINED' => $forms_language->get('forms', 'none_fields_defined'),
                'ARE_YOU_SURE' => $language->get('general', 'are_you_sure'),
                'CONFIRM_DELETE_FIELD' => $forms_language->get('forms', 'delete_field'),
                'YES' => $language->get('general', 'yes'),
                'NO' => $language->get('general', 'no')
            ]);
            
            $template_file = 'forms/form_fields.tpl';
        break;
        case 'permissions':
            // Form permissions
            if (Input::exists()) {
                $errors = [];
                
                if (Token::check(Input::get('token'))) {
                    // Display navigation link for guest?
                    if (isset($_POST['guest']) && $_POST['guest'] == 'on') $guest = 1;
                    else $guest = 0;
                    
                    // Save to database
                    $form->update([
                        'guest' => $guest
                    ]);
                    
                    // Update form permissions
                    $post = Input::get('perm-post-0');
                    $view_own = Input::get('perm-view_own-0');
                    $view_submissions = 0;
                    $delete_submissions = 0;
                    
                    $groups = DB::getInstance()->query('SELECT id FROM nl2_groups')->results();
                    $form_perm_query = DB::getInstance()->get('forms_permissions', ['form_id', '=', $form->data()->id])->results();
                    
                    $cat_perm_exists = 0;
                    if (count($form_perm_query)) {
                        foreach ($form_perm_query as $query) {
                            if ($query->group_id == 0) {
                                $cat_perm_exists = 1;
                                $update_id = $query->id;
                                break;
                            }
                        }
                    }
                    
                    try {
                        if ($cat_perm_exists != 0) { // Permission already exists, update
                            // Update the category
                            DB::getInstance()->update('forms_permissions', $update_id, [
                                'post' => $post,
                                'view_own' => $view_own,
                                'view' => $view_submissions,
                                'can_delete' => $delete_submissions
                            ]);
                        } else {
                            // Permission doesn't exist, create
                            DB::getInstance()->insert('forms_permissions', [
                                'group_id' => 0,
                                'form_id' => $form->data()->id,
                                'post' => $post,
                                'view_own' => $view_own,
                                'view' => $view_submissions,
                                'can_delete' => $delete_submissions
                            ]);
                        }
                    } catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                    
                    foreach ($groups as $group) {
                        $post = Input::get('perm-post-' . $group->id);
                        $view_own = Input::get('perm-view_own-' . $group->id);
                        $view_submissions = Input::get('perm-view_submissions-' . $group->id);
                        $delete_submissions = Input::get('perm-delete_submissions-' . $group->id);
                        
                        if (!($post)) $post = 0;
                        if (!($view_own)) $view_own = 0;
                        if (!($view_submissions)) $view_submissions = 0;
                        if (!($delete_submissions)) $delete_submissions = 0;
                        
                        $cat_perm_exists = 0;
                        if (count($form_perm_query)) {
                            foreach ($form_perm_query as $query) {
                                if ($query->group_id == $group->id) {
                                    $cat_perm_exists = 1;
                                    $update_id = $query->id;
                                    break;
                                }
                            }
                        }
                        
                        try {
                            if ($cat_perm_exists != 0) {
                                // Permission already exists, update
                                DB::getInstance()->update('forms_permissions', $update_id, [
                                    'post' => $post,
                                    'view_own' => $view_own,
                                    'view' => $view_submissions,
                                    'can_delete' => $delete_submissions
                                ]);
                            } else {
                                // Permission doesn't exist, create
                                DB::getInstance()->insert('forms_permissions', [
                                    'group_id' => $group->id,
                                    'form_id' => $form->data()->id,
                                    'post' => $post,
                                    'view_own' => $view_own,
                                    'view' => $view_submissions,
                                    'can_delete' => $delete_submissions
                                ]);
                            }
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    }
                    
                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form='.$form->data()->id.'&action=permissions'));
                } else
                    $errors[] = $language->get('general', 'invalid_token');
            }
            
            $guest_query = DB::getInstance()->query('SELECT 0 AS id, post AS can_post, view_own AS can_view_own FROM nl2_forms_permissions WHERE group_id = 0 AND form_id = ?', [$form->data()->id])->results();
            $group_query = DB::getInstance()->query('SELECT id, name, can_post, can_view_own, can_view, can_delete FROM nl2_groups A LEFT JOIN (SELECT group_id, post AS can_post, `view_own` AS can_view_own, `view` AS can_view, can_delete FROM nl2_forms_permissions WHERE form_id = ?) B ON A.id = B.group_id ORDER BY `order` ASC', [$form->data()->id])->results();
        
            $smarty->assign([
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
            ]);
            
            $template_file = 'forms/form_permissions.tpl';
        break;
        case 'statuses':
            // Form statuses
            if (Input::exists()) {
                $errors = [];
                
                if (Token::check(Input::get('token'))) {
                    $selected_statuses = (isset($_POST['status']) && is_array($_POST['status']) ? $_POST['status'] : []);
                    
                    // Get statuses from database
                    $statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
                    foreach ($statuses as $status) {
                        $forms = (!empty($status->fids) ? explode(',', $status->fids) : []);
                        
                        if (in_array($status->id, $selected_statuses)) {
                            // Add Status
                            if (!in_array($form->data()->id, $forms)) {
                                $forms[] = $form->data()->id;
                            }
                        } else {
                            // Remove status from form
                            if (in_array($form->data()->id, $forms)) {
                                if (($key = array_search($form->data()->id, $forms)) !== false) {
                                    unset($forms[$key]);
                                }
                            }
                        }

                        // Create string containing selected forms IDs
                        $forms_string = '';
                        foreach ($forms as $item) {
                            // Turn array of inputted forms into string of forms
                            $forms_string .= $item . ',';
                        }
                        $forms_string = rtrim($forms_string, ',');
                        
                        // Update database
                        DB::getInstance()->update('forms_statuses', $status->id, [
                            'fids' => $forms_string,
                        ]);
                    }
                    
                    // Save to database
                    $form->update([
                        'comment_status' => Output::getClean($_POST['comment_status'])
                    ]);
                    
                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form='.$form->data()->id.'&action=statuses'));
                } else
                    $errors[] = $language->get('general', 'invalid_token');
            }
            
            // Get statuses from database
            $statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0')->results();
            $status_array = [];
            if (count($statuses)) {
                foreach ($statuses as $status) {
                    $forms = (!empty($status->fids) ? explode(',', $status->fids) : []);
                    
                    $status_array[] = [
                        'id' => $status->id,
                        'html' => Output::getPurified($status->html),
                        'selected' => in_array($form->data()->id, $forms)
                    ];
                }
            }

            $smarty->assign([
                'SELECT_STATUSES' => $forms_language->get('forms', 'select_statuses_to_form'),
                'ALL_STATUSES' => $status_array,
                'CHANGE_STATUS_ON_COMMENT' => $forms_language->get('forms', 'change_status_on_comment'),
                'COMMENT_STATUS_VALUE' => $form->data()->comment_status,
                'DISABLED' => $language->get('user', 'disabled'),
            ]);
            
            $template_file = 'forms/form_statuses.tpl';
        break;
        case 'limits_requirements':
            // Limits and requirements
            if (Input::exists()) {
                $errors = [];

                if (Token::check(Input::get('token'))) {
                    $global_limit = [
                        'limit' => $_POST['global_limit'] ?? 0,
                        'interval' => $_POST['global_limit_interval'] ?? 1,
                        'period' => $_POST['global_limit_period'] ?? 'no_period'
                    ];

                    $user_limit = [
                        'limit' => $_POST['user_limit'] ?? 0,
                        'interval' => $_POST['user_limit_interval'] ?? 1,
                        'period' => $_POST['user_limit_period'] ?? 'no_period'
                    ];

                    $player_age = [
                        'interval' => $_POST['player_age_interval'] ?? 0,
                        'period' => $_POST['player_age_period'] ?? 'hour'
                    ];

                    $player_playtime = [
                        'playtime' => $_POST['player_playtime'] ?? 0,
                        'interval' => $_POST['player_playtime_interval'] ?? 1,
                        'period' => $_POST['player_playtime_period'] ?? 'all_time'
                    ];

                    $required_integrations = $_POST['required_integrations'];

                    $form->update([
                        'global_limit' => json_encode($global_limit),
                        'user_limit' => json_encode($user_limit),
                        'min_player_age' => json_encode($player_age),
                        'min_player_playtime' => json_encode($player_playtime),
                        'required_integrations' =>  json_encode(isset($required_integrations) && is_array($required_integrations) ? $required_integrations : [])
                    ]);

                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form='.$form->data()->id.'&action=limits_requirements'));
                } else {
                    // Invalid token
                    $errors[] = $language->get('general', 'invalid_token');
                }
            }

            $global_limit_json = json_decode($form->data()->global_limit, true) ?? [];
            $global_limit = [
                'limit' => $global_limit_json['limit'] ?? 0,
                'interval' => $global_limit_json['interval'] ?? 1,
                'period' => $global_limit_json['period'] ?? 'no_period'
            ];

            $user_limit_json = json_decode($form->data()->user_limit, true) ?? [];
            $user_limit = [
                'limit' => $user_limit_json['limit'] ?? 0,
                'interval' => $user_limit_json['interval'] ?? 1,
                'period' => $user_limit_json['period'] ?? 'no_period'
            ];

            $integrations_list = [];
            $selected_integrations = json_decode($form->data()->required_integrations, true) ?? [];
            foreach (Integrations::getInstance()->getEnabledIntegrations() as $item) {
                $integrations_list[] = [
                    'id' => $item->data()->id,
                    'name' => Output::getClean($item->getName()),
                    'selected' => in_array($item->data()->id, $selected_integrations)
                ];
            }

            $player_age_json = json_decode($form->data()->min_player_age, true) ?? [];
            $player_age = [
                'interval' => $player_age_json['interval'] ?? 0,
                'period' => $player_age_json['period'] ?? 'hour'
            ];

            $player_playtime_json = json_decode($form->data()->min_player_playtime, true) ?? [];
            $player_playtime = [
                'playtime' => $player_playtime_json['playtime'] ?? 0,
                'interval' => $player_playtime_json['interval'] ?? 1,
                'period' => $player_playtime_json['period'] ?? 'all_time'
            ];

            $smarty->assign([
                'GLOBAL_LIMIT_VALUE' => $global_limit,
                'USER_LIMIT_VALUE' => $user_limit,
                'INTEGRATIONS_LIST' => $integrations_list,
                'MCSTATISTICS_ENABLED' => Util::isModuleEnabled('MCStatistics'),
                'PLAYER_AGE_VALUE' => $player_age,
                'PLAYER_PLAYTIME_VALUE' => $player_playtime,
            ]);

            $template_file = 'forms/form_limits_requirements.tpl';
        break;
        case 'advanced':
            // Form advanced
            if (Input::exists()) {
                $errors = [];

                if (Token::check(Input::get('token'))) {
                    if (isset($_POST['hooks']) && count($_POST['hooks'])) {
                        $hooks = json_encode($_POST['hooks']);
                    } else {
                        $hooks = null;
                    }

                    $form->update([
                        'source' => Input::get('submission_source'),
                        'forum_id' => isset($_POST['forum']) ? Input::get('forum') : $form->data()->forum_id,
                        'hooks' => $hooks,
                    ]);

                    Session::flash('staff_forms', $forms_language->get('forms', 'form_updated_successfully'));
                    Redirect::to(URL::build('/panel/form/', 'form='.$form->data()->id.'&action=advanced'));
                } else
                    $errors[] = $language->get('general', 'invalid_token');
            }

            // Submission sources
            $submission_sources = [];
            $submission_sources[] = [
                'value' => 'forms',
                'name' => 'Forms (Default)'
            ];

            // Forum enabled?
            $forum_enabled = Util::isModuleEnabled('Forum');
            if ($forum_enabled) {
                $submission_sources[] = [
                    'value' => 'forum',
                    'name' => 'Forum'
                ];

                $forum_list = [];
                $forums = DB::getInstance()->orderAll('forums', 'forum_order', 'ASC')->results();
                foreach ($forums as $forum) {
                    $forum_list[] = [
                        'id' => $forum->id,
                        'title' => Output::getClean($forum->forum_title)
                    ];
                }

                $smarty->assign([
                    'SUBMIT_TO_FORUM' => 'Submit submission to forum?',
                    'SUBMIT_TO_FORUM_VALUE' => Output::getClean($form->data()->forum_id),
                    'SUBMIT_TO_FORUM_LIST' => $forum_list,
                ]);
            }

            // Hookd
            $hooks_query = DB::getInstance()->orderAll('hooks', 'id', 'ASC')->results();
            $hooks_array = [];
            if (count($hooks_query)) {
                foreach ($hooks_query as $hook) {
                    $events = json_decode($hook->events);

                    if (in_array('newFormSubmission', $events) || in_array('updatedFormSubmission', $events) || in_array('updatedFormSubmissionStaff', $events)) {
                        $hooks_array[] = [
                            'id' => $hook->id,
                            'name' => Output::getClean($hook->name),
                        ];
                    }
                }
            }

            $form_hooks = $form->data()->hooks ?: '[]';

            $smarty->assign([
                'SUBMISSION_SOURCE' => 'Submit submission to source',
                'SUBMISSION_SOURCE_LIST' => $submission_sources,
                'SUBMISSION_SOURCE_VALUE' => Output::getClean($form->data()->source),
                'INCLUDE_IN_HOOK' => 'Limit forms events to certain webhooks? (None selected will use all webhooks) ',
                'INFO' => $language->get('general', 'info'),
                'HOOK_SELECT_INFO' => 'Only webhooks with \'New form submission\' or \'New form submission comment\' or \'New form submission comment from staff\' selected as events are shown.',
                'HOOKS_ARRAY' => $hooks_array,
                'FORM_HOOKS' => json_decode($form_hooks),
                'NO_ITEM_SELECTED' => $language->get('admin', 'no_item_selected'),
            ]);

            $template_file = 'forms/form_advanced.tpl';
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
    'EDITING_FORM' => $forms_language->get('forms', 'editing_x', ['form' => Output::getClean($form->data()->title)]),
    'BACK' => $language->get('general', 'back'),
    'BACK_LINK' => URL::build('/panel/forms'),
    'INFO' => $language->get('general', 'info'),
    'FORMS' => $forms_language->get('forms', 'forms'),
    'TOKEN' => Token::get(),
    'SUBMIT' => $language->get('general', 'submit'),
    'GENERAL_SETTINGS' => $language->get('admin', 'general_settings'),
    'GENERAL_SETTINGS_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id),
    'FIELDS' => $forms_language->get('forms', 'fields'),
    'FIELDS_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&action=fields'),
    'PERMISSIONS' => $language->get('admin', 'permissions'),
    'PERMISSIONS_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&action=permissions'),
    'STATUSES' => $forms_language->get('forms', 'statuses'),
    'STATUSES_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&action=statuses'),
    'LIMITS_AND_REQUIREMENTS' => $forms_language->get('forms', 'limits_and_requirements'),
    'LIMITS_AND_REQUIREMENTS_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&action=limits_requirements'),
    'ADVANCED' => $forms_language->get('forms', 'advanced'),
    'ADVANCED_LINK' => URL::build('/panel/form/', 'form='.$form->data()->id.'&action=advanced'),
    'GUEST_VALUE' => $form->data()->guest
]);

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate($template_file, $smarty);