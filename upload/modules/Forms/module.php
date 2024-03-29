<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.1.2
 *
 *  License: MIT
 *
 *  Forms module file
 */

class Forms_Module extends Module {
    private DB $_db;
    private Language $_language;
    private Language $_forms_language;
    private Cache $_cache;

    public function __construct($language, $forms_language, $pages, $user, $navigation, $cache, $endpoints)
    {
        $this->_db = DB::getInstance();
        $this->_language = $language;
        $this->_forms_language = $forms_language;
        $this->_cache = $cache;

        $name = 'Forms';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a> and my <a href="https://partydragen.com/supporters/" target="_blank">Sponsors</a>';
        $module_version = '1.11.1';
        $nameless_version = '2.1.2';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('Forms', '/panel/form', 'pages/panel/form.php');
        $pages->add('Forms', '/panel/forms', 'pages/panel/forms.php');
        $pages->add('Forms', '/panel/forms/statuses', 'pages/panel/statuses.php');
        $pages->add('Forms', '/panel/forms/submissions', 'pages/panel/submissions.php');
        $pages->add('Forms', '/user/submissions', 'pages/user/submissions.php');

        // Check if module version changed
        $cache->setCache('forms_module_cache');
        if (!$cache->isCached('module_version')) {
            $cache->store('module_version', $module_version);
        } else {
            if ($module_version != $cache->retrieve('module_version')) {
                // Version have changed, Perform actions
                $this->initialiseUpdate($cache->retrieve('module_version'));

                $cache->store('module_version', $module_version);

                if ($cache->isCached('update_check')) {
                    $cache->erase('update_check');
                }
            }
        }

        try {
            $forms = $this->_db->query('SELECT id, link_location, url, icon, title, guest FROM nl2_forms')->results();
            if (count($forms)) {
                if ($user->isLoggedIn()) {
                    $group_ids = implode(',', $user->getAllGroupIds());
                } else {
                    $group_ids = implode(',', array(0));
                }

                foreach ($forms as $form) {
                    // Register form page
                    $pages->add('Forms', $form->url, 'pages/form.php', 'form-' . $form->id, true);

                    $perm = false;
                    if (!$user->isLoggedIn() && $form->guest == 1) {
                        $perm = true;
                    }

                    if (!$perm) {
                        $hasperm = $this->_db->query('SELECT form_id FROM nl2_forms_permissions WHERE form_id = ? AND post = 1 AND group_id IN(' . $group_ids . ')', array($form->id));
                        if ($hasperm->count()) {
                            $perm = true;
                        }
                    }

                    // Check cache first
                    $cache->setCache('navbar_order');
                    if (!$cache->isCached('form-' . $form->id . '_order')) {
                        // Create cache entry now
                        $form_order = 5;
                        $cache->store('form-' . $form->id . '_order', 5);
                    } else {
                        $form_order = $cache->retrieve('form-' . $form->id . '_order');
                    }

                    // Add link location to navigation if user have permission
                    if ($perm) {
                        switch ($form->link_location) {
                            case 1:
                                // Navbar
                                $navigation->add('form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'top', null, $form_order, $form->icon);
                                break;
                            case 2:
                                // "More" dropdown
                                $navigation->addItemToDropdown('more_dropdown', 'form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'top', null, $form->icon, $form_order);
                                break;
                            case 3:
                                // Footer
                                $navigation->add('form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'footer', null, $form_order, $form->icon);
                                break;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Database tables don't exist yet
        }

        // Hooks
        EventHandler::registerEvent(SubmissionCreatedEvent::class);
        EventHandler::registerEvent(SubmissionUpdatedEvent::class);
        EventHandler::registerEvent(SubmissionUpdatedStaffEvent::class);
        EventHandler::registerEvent('renderForm', 'renderForm', [], true, true);

        EventHandler::registerListener('renderForm', [ContentHook::class, 'purify']);
        EventHandler::registerListener('renderForm', [ContentHook::class, 'codeTransform'], 15);
        EventHandler::registerListener('renderForm', [ContentHook::class, 'decode'], 20);
        EventHandler::registerListener('renderForm', [ContentHook::class, 'renderEmojis'], 10);
        EventHandler::registerListener('renderForm', [ContentHook::class, 'replaceAnchors'], 5);
        EventHandler::registerListener(GroupClonedEvent::class, CloneGroupFormsHook::class);
        EventHandler::registerListener(UserDeletedEvent::class, DeleteUserFormsHook::class);

        $endpoints->loadEndpoints(ROOT_PATH . '/modules/Forms/includes/endpoints');

        Endpoints::registerTransformer('form', 'Forms', static function (Nameless2API $api, string $value) {
            if (is_numeric($value)) {
                // Get form by id
                $form = new Form($value);
            } else {
                // Get form by url
                $form = new Form('/' . $value, 'url');
            }

            if ($form->exists()) {
                return $form;
            }

            $api->throwError(FormsApiErrors::ERROR_FORM_NOT_FOUND);
        });

        Endpoints::registerTransformer('submission', 'Forms', static function (Nameless2API $api, string $value) {
            $submission = new Submission($value);
            if ($submission->exists()) {
                return $submission;
            }

            $api->throwError(FormsApiErrors::ERROR_SUBMISSION_NOT_FOUND);
        });
    }

    public function onInstall() {
        // Initialise
        $this->initialise();
    }

    public function onUninstall() {
        
    }

    public function onEnable() {
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable() {
        // No actions necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
        // Permissions
        PermissionHandler::registerPermissions('Forms', array(
            'forms.view-submissions' => $this->_forms_language->get('forms', 'forms_view_submissions'),
            'forms.manage' => $this->_forms_language->get('forms', 'forms_manage'),
            'forms.anonymous' => $this->_language->get('moderator', 'staff_cp')  . ' &raquo; ' .  $this->_forms_language->get('forms', 'forms')  . ' &raquo; ' . $this->_forms_language->get('forms', 'submit_as_anonymous')
        ));

        $navs[1]->add('cc_submissions', $this->_forms_language->get('forms', 'submissions'), URL::build('/user/submissions'));

        if (defined('BACK_END')) {
            if ($user->hasPermission('forms.manage') || $user->hasPermission('forms.view-submissions')) {
                $cache->setCache('panel_sidebar');
                if (!$cache->isCached('forms_order')) {
                    $order = 14;
                    $cache->store('forms_order', 14);
                } else {
                    $order = $cache->retrieve('forms_order');
                }
                $navs[2]->add('forms_divider', mb_strtoupper($this->_forms_language->get('forms', 'forms'), 'UTF-8'), 'divider', 'top', null, $order, '');

                if ($user->hasPermission('forms.manage')) {
                    if (!$cache->isCached('forms_icon')) {
                        $icon = '<i class="nav-icon fas fa-cogs"></i>';
                        $cache->store('forms_icon', $icon);
                    } else {
                        $icon = $cache->retrieve('forms_icon');
                    }
                    $navs[2]->add('forms', $this->_forms_language->get('forms', 'forms'), URL::build('/panel/forms'), 'top', null, $order + 0.1, $icon);
                }

                if ($user->hasPermission('forms.view-submissions')) {
                    if (!$cache->isCached('forms_submissions_icon')) {
                        $icon = '<i class="nav-icon fas fa-user-circle"></i>';
                        $cache->store('forms_submissions_icon', $icon);
                    } else {
                        $icon = $cache->retrieve('forms_submissions_icon');
                    }
                    $navs[2]->add('submissions', $this->_forms_language->get('forms', 'submissions'), URL::build('/panel/forms/submissions'), 'top', null, $order + 0.2, $icon);
                }
            }
        }

        // Check for module updates
        if (isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')) {
            // Page belong to this module?
            $page = $pages->getActivePage();
            if ($page['module'] == 'Forms') {

                $cache->setCache('forms_module_cache');
                if ($cache->isCached('update_check')) {
                    $update_check = $cache->retrieve('update_check');
                } else {
                    require_once(ROOT_PATH . '/modules/Forms/classes/Forms.php');
                    $update_check = Forms::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {  
                    $smarty->assign(array(
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_forms_language->get('forms', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->_forms_language->get('forms', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->_forms_language->get('forms', 'current_version_x', [
                            'version' => Output::getClean($this->getVersion())
                        ]),
                        'NEW_VERSION' => $this->_forms_language->get('forms', 'new_version_x', [
                            'new_version' => Output::getClean($update_check->new_version)
                        ]),
                        'NAMELESS_UPDATE' => $this->_forms_language->get('forms', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ));
                }
            }
        }
    }

    public function getDebugInfo(): array {
        // Forms
        $forms_list = [];
        $forms_query = $this->_db->query('SELECT * FROM nl2_forms')->results();
        foreach ($forms_query as $data) {
            $form = new Form($data->id);
            
            // Form fields
            $fields = [];
            foreach ($form->getFields() as $field) {
                $fields[] = [
                    'id' => (int)$field->id,
                    'name' => $field->name,
                    'type' => (int)$field->type,
                    'required' => (bool)$field->required,
                    'min' => (int)$field->min,
                    'max' => (int)$field->max,
                    'placeholder' => $field->placeholder,
                    'options' => $field->options,
                    'info' => $field->info,
                    'regex' => $field->regex,
                    'default_value' => $field->default_value,
                ];
            }

            // Form permissions
            $permissions = [];
            $permissions_query = $this->_db->query('SELECT * FROM nl2_forms_permissions WHERE form_id = ?', [$form->data()->id])->results();
            foreach ($permissions_query as $permission) {
               $permissions[] = [
                    'group_id' => (int)$permission->group_id,
                    'post' => (bool)$permission->post,
                    'view_own' => (bool)$permission->view_own,
                    'view' => (bool)$permission->view,
                    'can_delete' => (bool)$permission->can_delete,
                ]; 
            }

            $forms_list[] = [
                'id' => (int)$form->data()->id,
                'title' => $form->data()->title,
                'url' => $form->data()->url,
                'guest' => (bool)$form->data()->guest,
                'link_location' => (int)$form->data()->link_location,
                'icon' => $form->data()->icon,
                'captcha' => (bool)$form->data()->captcha,
                'comment_status' => (int)$form->data()->comment_status,
                'source' => $form->data()->source,
                'hooks' => $form->data()->hooks,
                'discord_fields' => (bool)$form->data()->discord_fields,
                'forum_id' => (int)$form->data()->forum_id,
                'fields' => $fields,
                'permissions' => $permissions
            ];
        }
        
        // Statuses
        $statuses_list = [];
        $statuses_query = $this->_db->query('SELECT * FROM nl2_forms_statuses')->results();
        foreach ($statuses_query as $data) {
            $statuses_list[] = [
                'id' => (int)$data->id,
                'html' => $data->html,
                'open' => (bool)$data->open,
                'fids' => $data->fids,
                'gids' => $data->gids,
                'color' => $data->color
            ];
        }
        
        return ['forms' => $forms_list, 'statuses' => $statuses_list];
    }

    private function initialiseUpdate($old_version) {
        $old_version = str_replace(array(".", "-"), "", $old_version);

        if ($old_version < 134) {
            try {
                $this->_db->addColumn('forms', '`captcha`', "tinyint(1) NOT NULL DEFAULT '0'");
                $this->_db->addColumn('forms', '`content`', "mediumtext NULL DEFAULT NULL");
            } catch (Exception $e) {
                // Error
            }
        }

        if ($old_version < 160) {
            try {
                if (!$this->_db->showTables('forms_permissions')) {
                    try {
                        $this->_db->createTable("forms_permissions", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `group_id` int(11) NOT NULL, `post` tinyint(1) NOT NULL DEFAULT '1', `view_own` tinyint(1) NOT NULL DEFAULT '1', `view` tinyint(1) NOT NULL DEFAULT '0', `can_delete` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");
                    } catch(Exception $e) {
                        // Error
                    }

                    $groups = $this->_db->query('SELECT id, staff FROM nl2_groups')->results();
                    $forms = $this->_db->query('SELECT * FROM nl2_forms')->results();
                    foreach ($forms as $form) {
                        $this->_db->insert('forms_permissions', array(
                            'group_id' => 0,
                            'form_id' => $form->id,
                            'post' => $form->guest,
                            'view_own' => 0,
                            'view' => 0,
                            'can_delete' => 0
                        ));

                        foreach ($groups as $group) {
                            $this->_db->insert('forms_permissions', array(
                                'group_id' => $group->id,
                                'form_id' => $form->id,
                                'post' => 1,
                                'view_own' => $form->can_view,
                                'view' => ($group->staff == 1 ? 1 : 0),
                                'can_delete' => ($group->staff == 1 ? 1 : 0)
                            ));
                        }
                    }
                }
            } catch (Exception $e) {
                // Error
            }
        }

        if ($old_version < 170) {
            try {
                $this->_db->addColumn('forms_comments', '`anonymous`', "tinyint(1) NOT NULL DEFAULT '0'");
                $this->_db->addColumn('forms_fields', '`min`', "int(11) NOT NULL DEFAULT '0'");
                $this->_db->addColumn('forms_fields', '`max`', "int(11) NOT NULL DEFAULT '0'");
                $this->_db->addColumn('forms_fields', '`placeholder`', "varchar(255) NULL DEFAULT NULL");
                $this->_db->addColumn('forms', '`comment_status`', "int(11) NOT NULL DEFAULT '0'");

                // Update main admin group permissions
                $group = $this->_db->get('groups', array('id', '=', 2))->results();
                $group = $group[0];

                $group_permissions = json_decode($group->permissions, TRUE);
                $group_permissions['forms.anonymous'] = 1;

                $group_permissions = json_encode($group_permissions);
                $this->_db->update('groups', 2, array('permissions' => $group_permissions));
            } catch (Exception $e) {
                // Error
            }
        }

        if ($old_version < 180) {
            try {
                // Generate table
                $this->_db->createTable("forms_replies_fields", " `id` int(11) NOT NULL AUTO_INCREMENT, `submission_id` int(11) NOT NULL, `field_id` int(11) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`)");
                $this->_db->query('ALTER TABLE `nl2_forms_replies_fields` ADD INDEX `nl2_forms_replies_fields_idx_submission_id` (`submission_id`)');
            } catch (Exception $e) {
                // Error
            }

            try {
                $this->_db->addColumn('forms_fields', '`info`', "text NULL");
            } catch (Exception $e) {
                // Error
            }
        }

        if ($old_version < 192) {
            try {
                $this->_db->addColumn('forms', '`source`', "varchar(32) NOT NULL DEFAULT 'forms'");
                $this->_db->addColumn('forms', '`forum_id`', "int(11) NOT NULL DEFAULT '0'");
                $this->_db->addColumn('forms_statuses', '`color`', "varchar(32) NULL DEFAULT NULL");
            } catch (Exception $e) {
                // Error
            }
        }

        if ($old_version < 1102) {
            try {
                $this->_db->query('ALTER TABLE nl2_forms ADD `user_limit` varchar(128) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms ADD `global_limit` varchar(128) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms ADD `required_integrations` varchar(128) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms ADD `min_player_age` varchar(128) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms ADD `min_player_playtime` varchar(128) DEFAULT NULL');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }

            try {
                $this->_db->query('ALTER TABLE nl2_forms ADD `hooks` varchar(512) DEFAULT NULL');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }

            try {
                $this->_db->query('ALTER TABLE nl2_forms_fields ADD `regex` varchar(64) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms_fields ADD `default_value` varchar(64) NOT NULL DEFAULT \'\'');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }
        }

        if ($old_version < 1103) {
            try {
                $this->_db->query('ALTER TABLE nl2_forms_fields ADD `regex` varchar(64) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms_fields ADD `default_value` varchar(64) NOT NULL DEFAULT \'\'');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }
        }

        if ($old_version < 1110) {
            try {
                $this->_db->query('ALTER TABLE nl2_forms ADD `discord_fields` tinyint(1) NOT NULL DEFAULT \'0\'');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }

            try {
                $this->_db->query('ALTER TABLE nl2_forms_replies ADD `source` varchar(64) DEFAULT NULL');
                $this->_db->query('ALTER TABLE nl2_forms_replies ADD `source_id` int(11) DEFAULT NULL');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }

            try {
                $this->_db->query('ALTER TABLE nl2_forms_comments ADD `staff_only` tinyint(1) NOT NULL DEFAULT \'0\'');
            } catch (Exception $e) {
                // unable to retrieve from config
                echo $e->getMessage() . '<br />';
            }
        }
    }

    private function initialise() {
        // Generate tables
        if (!$this->_db->showTables('forms')) {
            try {
                $this->_db->createTable("forms", " `id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(32) NOT NULL, `title` varchar(32) NOT NULL, `guest` tinyint(1) NOT NULL DEFAULT '0', `link_location` tinyint(1) NOT NULL DEFAULT '1', `icon` varchar(64) NULL, `can_view` tinyint(1) NOT NULL DEFAULT '0', `captcha` tinyint(1) NOT NULL DEFAULT '0', `content` mediumtext NULL DEFAULT NULL, `comment_status` int(11) NOT NULL DEFAULT '0', `source` varchar(32) NOT NULL DEFAULT 'forms', `forum_id` int(11) NOT NULL DEFAULT '0', `global_limit` varchar(128) DEFAULT NULL, `user_limit` varchar(128) DEFAULT NULL, `required_integrations` varchar(128) DEFAULT NULL, `min_player_age` varchar(128) DEFAULT NULL, `min_player_playtime` varchar(128) DEFAULT NULL, `hooks` varchar(512) DEFAULT NULL, `discord_fields` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");

                $this->_db->insert('forms', array(
                    'url' => '/apply',
                    'title' => 'Staff Applications',
                    'guest' => 0,
                    'link_location' => 1
                ));
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$this->_db->showTables('forms_permissions')) {
            try {
                $this->_db->createTable("forms_permissions", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `group_id` int(11) NOT NULL, `post` tinyint(1) NOT NULL DEFAULT '1', `view_own` tinyint(1) NOT NULL DEFAULT '1', `view` tinyint(1) NOT NULL DEFAULT '0', `can_delete` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");

                $groups = $this->_db->query('SELECT id, staff FROM nl2_groups')->results();
                $this->_db->insert('forms_permissions', array(
                    'group_id' => 0,
                    'form_id' => 1,
                    'post' => 0,
                    'view_own' => 0,
                    'view' => 0,
                    'can_delete' => 0
                ));

                foreach ($groups as $group) {
                    $this->_db->insert('forms_permissions', array(
                        'group_id' => $group->id,
                        'form_id' => 1,
                        'post' => 1,
                        'view_own' => 1,
                        'view' => ($group->staff == 1 ? 1 : 0),
                        'can_delete' => ($group->staff == 1 ? 1 : 0)
                    ));
                }
            } catch (Exception $e) {
                // Error
            }
        }  

        if (!$this->_db->showTables('forms_comments')) {
            try {
                $this->_db->createTable("forms_comments", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `created` int(11) NOT NULL, `anonymous` tinyint(1) NOT NULL DEFAULT '0', `content` mediumtext NOT NULL, `staff_only` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$this->_db->showTables('forms_fields')) {
            try {
                $this->_db->createTable("forms_fields", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `name` varchar(255) NOT NULL, `type` int(11) NOT NULL, `required` tinyint(1) NOT NULL DEFAULT '0', `min` int(11) NOT NULL DEFAULT '0', `max` int(11) NOT NULL DEFAULT '0', `placeholder` varchar(255) NULL DEFAULT NULL, `options` text NULL, `info` text NULL, `regex` varchar(64) DEFAULT NULL, `default_value` varchar(64) NOT NULL DEFAULT '', `deleted` tinyint(1) NOT NULL DEFAULT '0', `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)");
                
                $this->_db->insert('forms_fields', array(
                    'form_id' => 1,
                    'name' => 'Minecraft Name',
                    'type' => 1,
                    'required' => 1,
                    'order' => 1
                ));
                $this->_db->insert('forms_fields', array(
                    'form_id' => 1,
                    'name' => 'Why you want to become staff?',
                    'type' => 3,
                    'required' => 1,
                    'order' => 2
                ));
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$this->_db->showTables('forms_replies')) {
            try {
                $this->_db->createTable("forms_replies", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NULL, `updated_by` int(11) NULL, `created` int(11) NOT NULL, `updated` int(11) NOT NULL, `content` mediumtext NULL DEFAULT NULL, `status_id` int(11) NOT NULL DEFAULT '1', `source` varchar(32) DEFAULT NULL, `source_id` int(11) DEFAULT NULL, PRIMARY KEY (`id`)");
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$this->_db->showTables('forms_replies_fields')) {
            try {
                $this->_db->createTable("forms_replies_fields", " `id` int(11) NOT NULL AUTO_INCREMENT, `submission_id` int(11) NOT NULL, `field_id` int(11) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`)");
                
                $this->_db->query('ALTER TABLE `nl2_forms_replies_fields` ADD INDEX `nl2_forms_replies_fields_idx_submission_id` (`submission_id`)');
            } catch (Exception $e) {
                // Error
            }
        }

        if (!$this->_db->showTables('forms_statuses')) {
            try {
                $this->_db->createTable("forms_statuses", " `id` int(11) NOT NULL AUTO_INCREMENT, `html` varchar(1024) NOT NULL, `open` tinyint(1) NOT NULL, `fids` varchar(128) NULL, `gids` varchar(128) NULL, `color` varchar(32) NULL DEFAULT NULL, `deleted` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");
                
                $this->_db->insert('forms_statuses', array(
                    'html' => '<span class="badge badge-success">Open</span>',
                    'open' => 1,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
                $this->_db->insert('forms_statuses', array(
                    'html' => '<span class="badge badge-danger">Closed</span>',
                    'open' => 0,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
                $this->_db->insert('forms_statuses', array(
                    'html' => '<span class="badge badge-warning">Under Considering</span>',
                    'open' => 1,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
            } catch (Exception $e) {
                // Error
            }
        }

        try {
            // Update main admin group permissions
            $group = $this->_db->get('groups', array('id', '=', 2))->results();
            $group = $group[0];
            
            $group_permissions = json_decode($group->permissions, TRUE);
            $group_permissions['forms.manage'] = 1;
            $group_permissions['forms.view-submissions'] = 1;
            $group_permissions['forms.manage-submission'] = 1;
            $group_permissions['forms.anonymous'] = 1;
            
            $group_permissions = json_encode($group_permissions);
            $this->_db->update('groups', 2, array('permissions' => $group_permissions));
        } catch (Exception $e) {
            // Error
        }
    }
}
