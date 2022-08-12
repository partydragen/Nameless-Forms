<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.1
 *
 *  Delete user event listener for Forms module
 */

class DeleteUserFormsHook {
    public static function execute(array $params = []): void {
        if (isset($params['user_id']) && $params['user_id'] > 1) {
            $db = DB::getInstance();

            // Delete the user's submissions
            $db->delete('forms_replies', ['user_id', $params['user_id']]);

            // Delete the user's submissions comments
            $db->delete('forms_comments', ['user_id', $params['user_id']]);
        }
    }
}