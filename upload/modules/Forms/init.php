<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Forms module initialisation file
 */
 
// Initialise forms language
$forms_language = new Language(ROOT_PATH . '/modules/Forms/language', LANGUAGE);
 
// Define URLs which belong to this module
$pages->add('Forms', '/admin/forms', 'pages/admin/forms.php');
$pages->add('Forms', '/admin/form', 'pages/admin/form.php');

// Add link to admin sidebar
$admin_sidebar['forms'] = array(
	'title' => $forms_language->get('forms', 'forms'),
	'url' => URL::build('/admin/forms')
);

// Register Forms
$forms = $queries->getWhere('forms', array('id', '<>', 0));
if(count($forms)){
	foreach($forms as $form){
		// Register form page
		$pages->add('Forms', $form->url, 'pages/form.php', 'form', true);
		
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