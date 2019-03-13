<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Forms module file
 */

class Forms_Module extends Module {
	private $_language;
	private $_forms_language;

	public function __construct($language, $forms_language, $pages, $queries, $navigation, $cache){
		$this->_language = $language;
		$this->_forms_language = $forms_language;

		$name = 'Forms';
		$author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
		$module_version = '1.0.0';
		$nameless_version = '2.0.0-pr5';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Define URLs which belong to this module
		$pages->add('Forms', '/panel/form', 'pages/panel/form.php');
		$pages->add('Forms', '/panel/forms', 'pages/panel/forms.php');
		$pages->add('Forms', '/panel/forms/statuses', 'pages/panel/statuses.php');
		$pages->add('Forms', '/panel/forms/submissions', 'pages/panel/submissions.php');
		
		$forms = $queries->getWhere('forms', array('id', '<>', 0));
		if(count($forms)){
			foreach($forms as $form){
				// Register form page
				$pages->add('Forms', $form->url, 'pages/form.php', $form->title, true);
				
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

	public function onInstall(){
		// Not necessary for Forms
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
		
		if(defined('BACK_END')){
			if($user->hasPermission('forms.view') || $user->hasPermission('forms.edit')){
				$cache->setCache('panel_sidebar');
				if(!$cache->isCached('forms_order')){
					$order = 12;
					$cache->store('forms_order', 12);
				} else {
					$order = $cache->retrieve('forms_order');
				}
				$navs[2]->add('forms_divider', mb_strtoupper($this->_forms_language->get('forms', 'forms'), 'UTF-8'), 'divider', 'top', null, $order, '');
				
				if($user->hasPermission('forms.edit')){
					if(!$cache->isCached('forms_icon')){
						$icon = '<i class="nav-icon fas fa-cogs"></i>';
						$cache->store('forms_icon', $icon);
					} else {
						$icon = $cache->retrieve('forms_icon');
					}
					$navs[2]->add('forms', $this->_forms_language->get('forms', 'forms'), URL::build('/panel/forms'), 'top', null, $order, $icon);
				}
				
				if($user->hasPermission('forms.view')){
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