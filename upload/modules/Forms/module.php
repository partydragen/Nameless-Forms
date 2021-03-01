<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  Forms module file
 */

class Forms_Module extends Module {
    private $_language;
    private $_forms_language;
    private $_cache;

    public function __construct($language, $forms_language, $pages, $queries, $navigation, $cache){
        $this->_language = $language;
        $this->_forms_language = $forms_language;
        $this->_cache = $cache;

        $name = 'Forms';
        $author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.4.0';
        $nameless_version = '2.0.0-pr9';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);
        
        // Hooks
        HookHandler::registerEvent('newFormSubmission', $forms_language->get('forms', 'new_form_submission'));
        HookHandler::registerEvent('updatedFormSubmission', $forms_language->get('forms', 'updated_form_submission'));

        // Define URLs which belong to this module
        $pages->add('Forms', '/panel/form', 'pages/panel/form.php');
        $pages->add('Forms', '/panel/forms', 'pages/panel/forms.php');
        $pages->add('Forms', '/panel/forms/statuses', 'pages/panel/statuses.php');
        $pages->add('Forms', '/panel/forms/submissions', 'pages/panel/submissions.php');
        $pages->add('Forms', '/user/submissions', 'pages/user/submissions.php');
        
        // Check if module version changed
        $cache->setCache('forms_module_cache');
        if(!$cache->isCached('module_version')){
            $cache->store('module_version', $module_version);
        } else {
            if($module_version != $cache->retrieve('module_version')) {
                // Version have changed, Perform actions
                $this->initialiseUpdate($cache->retrieve('module_version'));
        
                $cache->store('module_version', $module_version);
                
                if($cache->isCached('update_check')){
                    $cache->erase('update_check');
                }
            }
        }
        
        try {
            $forms = $queries->getWhere('forms', array('id', '<>', 0));
            if(count($forms)){
                foreach($forms as $form){
                    // Register form page
                    $pages->add('Forms', $form->url, 'pages/form.php', 'form-' . $form->id, true);
                    
                    // Add link location
                    switch($form->link_location){
                        case 1:
                            // Navbar
                            // Check cache first
                            $cache->setCache('navbar_order');
                            if(!$cache->isCached('form-' . $form->id . '_order')){
                                // Create cache entry now
                                $form_order = 5;
                                $cache->store('form-' . $form->id . '_order', 5);
                            } else {
                                $form_order = $cache->retrieve('form-' . $form->id . '_order');
                            }
                            $navigation->add('form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'top', null, $form_order, $form->icon);
                        break;
                        case 2:
                            // "More" dropdown
                            $navigation->addItemToDropdown('more_dropdown', 'form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'top', null, $form->icon);
                        break;
                        case 3:
                            // Footer
                            $navigation->add('form-' . $form->id, Output::getClean($form->title), URL::build(Output::getClean($form->url)), 'footer', null, 2000, $form->icon);
                        break;
                    }
                }
            }
        } catch(Exception $e){
            // Database tables don't exist yet
        }
    }

    public function onInstall(){
        // Initialise
        $this->initialise();
    }

    public function onUninstall(){
        
    }

    public function onEnable(){
        // Check if we need to initialise again
        $this->initialise();
    }

    public function onDisable(){
        // No actions necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
        // Permissions
        PermissionHandler::registerPermissions('Forms', array(
            'forms.view-submissions' => $this->_forms_language->get('forms', 'forms_view_submissions'),
            'forms.manage' => $this->_forms_language->get('forms', 'forms_manage'),
        ));
        
        $navs[1]->add('cc_submissions', $this->_forms_language->get('forms', 'submissions'), URL::build('/user/submissions'));
        
        if(defined('BACK_END')){
            if($user->hasPermission('forms.manage') || $user->hasPermission('forms.view-submissions')){
                $cache->setCache('panel_sidebar');
                if(!$cache->isCached('forms_order')){
                    $order = 14;
                    $cache->store('forms_order', 14);
                } else {
                    $order = $cache->retrieve('forms_order');
                }
                $navs[2]->add('forms_divider', mb_strtoupper($this->_forms_language->get('forms', 'forms'), 'UTF-8'), 'divider', 'top', null, $order, '');
                
                if($user->hasPermission('forms.manage')){
                    if(!$cache->isCached('forms_icon')){
                        $icon = '<i class="nav-icon fas fa-cogs"></i>';
                        $cache->store('forms_icon', $icon);
                    } else {
                        $icon = $cache->retrieve('forms_icon');
                    }
                    $navs[2]->add('forms', $this->_forms_language->get('forms', 'forms'), URL::build('/panel/forms'), 'top', null, $order + 0.1, $icon);
                }
                
                if($user->hasPermission('forms.view-submissions')){
                    if(!$cache->isCached('forms_submissions_icon')){
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
        if(isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')){
            if(rtrim($_GET['route'], '/') == '/panel/forms/submissions' || rtrim($_GET['route'], '/') == '/panel/forms' || rtrim($_GET['route'], '/') == '/panel/form' || rtrim($_GET['route'], '/') == '/panel/forms/statuses'){

                $cache->setCache('forms_module_cache');
                if($cache->isCached('update_check')){
                    $update_check = $cache->retrieve('update_check');
                } else {
                    require_once(ROOT_PATH . '/modules/Forms/classes/Forms.php');
                    $update_check = Forms::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if(!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)){  
                    $smarty->assign(array(
                        'NEW_UPDATE' => str_replace('{x}', $this->getName(), (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_forms_language->get('forms', 'new_urgent_update_available_x') : $this->_forms_language->get('forms', 'new_update_available_x')),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => str_replace('{x}', $this->getVersion(), $this->_forms_language->get('forms', 'current_version_x')),
                        'NEW_VERSION' => str_replace('{x}', Output::getClean($update_check->new_version), $this->_forms_language->get('forms', 'new_version_x')),
                        'UPDATE' => $this->_forms_language->get('forms', 'view_resource'),
                        'UPDATE_LINK' => 'https://partydragen.com/resources/resource/1-forms-module/'
                    ));
                }
            }
        }
    }
    
    private function initialiseUpdate($old_version){
        $old_version = str_replace(array(".", "-"), "", $old_version);
        $queries = new Queries();
        
        if($old_version < 134) {
            try {
                $queries->alterTable('forms', '`captcha`', "tinyint(1) NOT NULL DEFAULT '0'");
                $queries->alterTable('forms', '`content`', "mediumtext NULL DEFAULT NULL");
            } catch(Exception $e){
                // Error
            }
        }
    }
    
    private function initialise(){
        // Generate tables
        try {
            $engine = Config::get('mysql/engine');
            $charset = Config::get('mysql/charset');
        } catch(Exception $e){
            $engine = 'InnoDB';
            $charset = 'utf8mb4';
        }
        if(!$engine || is_array($engine))
            $engine = 'InnoDB';
        if(!$charset || is_array($charset))
            $charset = 'latin1';
        
        $queries = new Queries();
        if(!$queries->tableExists('forms')){
            try {
                $queries->createTable("forms", " `id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(32) NOT NULL, `title` varchar(32) NOT NULL, `guest` tinyint(1) NOT NULL DEFAULT '0', `link_location` tinyint(1) NOT NULL DEFAULT '1', `icon` varchar(64) NULL, `can_view` tinyint(1) NOT NULL DEFAULT '0', `captcha` tinyint(1) NOT NULL DEFAULT '0', `content` mediumtext NULL DEFAULT NULL, PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
                
                $queries->create('forms', array(
                    'url' => '/apply',
                    'title' => 'Staff Applications',
                    'guest' => 0,
                    'link_location' => 1
                    
                ));
            } catch(Exception $e){
                // Error
            }
        }
        
        if(!$queries->tableExists('forms_comments')){
            try {
                $queries->createTable("forms_comments", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `created` int(11) NOT NULL, `content` mediumtext NOT NULL, PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
            } catch(Exception $e){
                // Error
            }
        }
        
        if(!$queries->tableExists('forms_fields')){
            try {
                $queries->createTable("forms_fields", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `name` varchar(255) NOT NULL, `type` int(11) NOT NULL, `required` tinyint(1) NOT NULL DEFAULT '0', `options` text NULL, `deleted` tinyint(1) NOT NULL DEFAULT '0', `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
                
                $queries->create('forms_fields', array(
                    'form_id' => 1,
                    'name' => 'Minecraft Name',
                    'type' => 1,
                    'required' => 1,
                    'order' => 1
                ));
                $queries->create('forms_fields', array(
                    'form_id' => 1,
                    'name' => 'Why you want to become staff?',
                    'type' => 3,
                    'required' => 1,
                    'order' => 2
                ));
            } catch(Exception $e){
                // Error
            }
        }
        
        if(!$queries->tableExists('forms_replies')){
            try {
                $queries->createTable("forms_replies", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NULL, `updated_by` int(11) NULL, `created` int(11) NOT NULL, `updated` int(11) NOT NULL, `content` mediumtext NOT NULL, `status_id` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
            } catch(Exception $e){
                // Error
            }
        }
        
        if(!$queries->tableExists('forms_statuses')){
            try {
                $queries->createTable("forms_statuses", " `id` int(11) NOT NULL AUTO_INCREMENT, `html` varchar(1024) NOT NULL, `open` tinyint(1) NOT NULL, `fids` varchar(128) NULL, `gids` varchar(128) NULL, `deleted` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)", "ENGINE=$engine DEFAULT CHARSET=$charset");
                
                $queries->create('forms_statuses', array(
                    'html' => '<span class="badge badge-success">Open</span>',
                    'open' => 1,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
                $queries->create('forms_statuses', array(
                    'html' => '<span class="badge badge-danger">Closed</span>',
                    'open' => 0,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
                $queries->create('forms_statuses', array(
                    'html' => '<span class="badge badge-warning">Under Considering</span>',
                    'open' => 1,
                    'fids' => '1',
                    'gids' => '2,3'
                ));
            } catch(Exception $e){
                // Error
            }
        }
        
        try {
            // Update main admin group permissions
            $group = $queries->getWhere('groups', array('id', '=', 2));
            $group = $group[0];
            
            $group_permissions = json_decode($group->permissions, TRUE);
            $group_permissions['forms.manage'] = 1;
            $group_permissions['forms.view-submissions'] = 1;
            $group_permissions['forms.manage-submission'] = 1;
            
            $group_permissions = json_encode($group_permissions);
            $queries->update('groups', 2, array('permissions' => $group_permissions));
        } catch(Exception $e){
            // Error
        }
    }
}
