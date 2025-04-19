<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.2.0
 *
 *  License: MIT
 *
 *  Forms module initialisation file
 */
 
// Initialise forms language
$forms_language = new Language(ROOT_PATH . '/modules/Forms/language', LANGUAGE);

require_once(ROOT_PATH . '/modules/Forms/autoload.php');

// Initialise module
require_once(ROOT_PATH . '/modules/Forms/module.php');
$module = new Forms_Module($language, $forms_language, $pages, $user, $navigation, $cache, $endpoints);