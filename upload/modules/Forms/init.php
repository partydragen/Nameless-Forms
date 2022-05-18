<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Forms module initialisation file
 */
 
// Initialise forms language
$forms_language = new Language(ROOT_PATH . '/modules/Forms/language', LANGUAGE);

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Forms', 'classes', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

// Initialise module
require_once(ROOT_PATH . '/modules/Forms/module.php');
$module = new Forms_Module($language, $forms_language, $pages, $user, $queries, $navigation, $cache);