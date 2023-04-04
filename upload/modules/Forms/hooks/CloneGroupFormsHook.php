<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.1
 *
 *  Clone group event listener handler class
 */

class CloneGroupFormsHook {

    public static function execute(GroupClonedEvent $event): void {

        // Clone group permissions for forms
        $new_group_id = $event->group->id;
        $permissions = DB::getInstance()->query('SELECT * FROM nl2_forms_permissions WHERE group_id = ?', [$event->cloned_group->id]);
        if ($permissions->count()) {
            $permissions = $permissions->results();

            $inserts = [];
            foreach ($permissions as $permission) {
                $inserts[] = '(' .$new_group_id . ',' . $permission->form_id . ',' . $permission->post . ',' . $permission->view_own. ',' . $permission->view . ',' . $permission->can_delete . ')';
            }

            $query = 'INSERT INTO nl2_forms_permissions (group_id, form_id, post, view_own, view, can_delete) VALUES ';
            $query .= implode(',', $inserts);

            DB::getInstance()->query($query);
        }
    }
}