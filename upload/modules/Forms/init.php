<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 *
 *  Forms module initialisation file
 */
 
// Initialise forms language
$forms_language = new Language(ROOT_PATH . '/modules/Forms/language', LANGUAGE);

// Initialise module
require_once(ROOT_PATH . '/modules/Forms/module.php');
$module = new Forms_Module($language, $forms_language, $pages, $queries, $navigation, $cache);