<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.2.0
 *
 *  License: MIT
 *
 *  Forms module autoload file
 */

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Forms', 'classes', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Forms', 'classes', 'Events', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Forms', 'classes', 'SubmissionSources', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});

// Load classes
spl_autoload_register(function ($class) {
    $path = join(DIRECTORY_SEPARATOR, array(ROOT_PATH, 'modules', 'Forms', 'hooks', $class . '.php'));
    if (file_exists($path)) {
        require_once($path);
    }
});