<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr6
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
		$module_version = '1.0.0';
		$nameless_version = '2.0.0-pr6';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Define URLs which belong to this module
		$pages->add('Forms', '/panel/form', 'pages/panel/form.php');
		$pages->add('Forms', '/panel/forms', 'pages/panel/forms.php');
		$pages->add('Forms', '/panel/forms/statuses', 'pages/panel/statuses.php');
		$pages->add('Forms', '/panel/forms/submissions', 'pages/panel/submissions.php');
		$pages->add('Forms', '/user/submissions', 'pages/user/submissions.php');
		
		// is installed
		$cache->setCache('forms');
		if($cache->isCached('forms_installed')){
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
		}
	}

	public function onInstall(){
		// Queries
		$queries = new Queries();
		
		try {
			// Create tabels
			$data = $queries->createTable("forms", " `id` int(11) NOT NULL AUTO_INCREMENT, `url` varchar(32) NOT NULL, `title` varchar(32) NOT NULL, `guest` tinyint(1) NOT NULL DEFAULT '0', `link_location` tinyint(1) NOT NULL DEFAULT '1', `icon` varchar(64) NULL, `can_view` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$data = $queries->createTable("forms_comments", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, `created` int(11) NOT NULL, `content` mediumtext NOT NULL, PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$data = $queries->createTable("forms_fields", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `name` varchar(255) NOT NULL, `type` int(11) NOT NULL, `required` tinyint(1) NOT NULL DEFAULT '0', `options` text NULL, `deleted` tinyint(1) NOT NULL DEFAULT '0', `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$data = $queries->createTable("forms_replies", " `id` int(11) NOT NULL AUTO_INCREMENT, `form_id` int(11) NOT NULL, `user_id` int(11) NULL, `updated_by` int(11) NULL, `created` int(11) NOT NULL, `updated` int(11) NOT NULL, `content` mediumtext NOT NULL, `status_id` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$data = $queries->createTable("forms_statuses", " `id` int(11) NOT NULL AUTO_INCREMENT, `html` varchar(1024) NOT NULL, `open` tinyint(1) NOT NULL, `fids` varchar(128) NULL, `gids` varchar(128) NULL, `deleted` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
		} catch(Exception $e){
			// Error
		}
		
		try {
			// Insert data
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
			
			// create example staff applications
			$queries->create('forms', array(
				'url' => '/apply',
				'title' => 'Staff Applications',
				'guest' => 0,
				'link_location' => 1
				
			));
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
		
		// Installed
		$this->_cache->setCache('forms');
		if(!$this->_cache->isCached('forms_installed')){
			$this->_cache->store('forms_installed', true);
		}
	}

	public function onUninstall(){

	}

	public function onEnable(){
		// No actions necessary
	}

	public function onDisable(){
		// No actions necessary
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
		// Permissions
		PermissionHandler::registerPermissions('Forms', array(
			'forms.view-submissions' => $this->_language->get('admin', 'core'),
			'forms.manage-submission' => $this->_language->get('admin', 'core'),
			'forms.manage' => $this->_language->get('admin', 'core'),
		));
		
		$navs[1]->add('cc_submissions', $this->_forms_language->get('forms', 'submissions'), URL::build('/user/submissions'));
		
		if(defined('BACK_END')){
			if($user->hasPermission('forms.manage') || $user->hasPermission('forms.view-submissions')){
				$cache->setCache('panel_sidebar');
				if(!$cache->isCached('forms_order')){
					$order = 12;
					$cache->store('forms_order', 12);
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
					$navs[2]->add('forms', $this->_forms_language->get('forms', 'forms'), URL::build('/panel/forms'), 'top', null, $order, $icon);
				}
				
				if($user->hasPermission('forms.view-submissions')){
					if(!$cache->isCached('forms_submissions_icon')){
						$icon = '<i class="nav-icon fas fa-user-circle"></i>';
						$cache->store('forms_submissions_icon', $icon);
					} else {
						$icon = $cache->retrieve('forms_submissions_icon');
					}
					$navs[2]->add('submissions', $this->_forms_language->get('forms', 'submissions'), URL::build('/panel/forms/submissions'), 'top', null, $order, $icon);
				}
			}
		}
	}
}