<?php
class FormInfoEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/form/{form}';
        $this->_module = 'Forms';
        $this->_description = 'Get form details';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api, Form $form): void {
        $return = [
            'id' => $form->data()->id,
            'url' => $form->data()->url,
            'url_full' => rtrim(URL::getSelfURL(), '/') . URL::build($form->data()->url),
            'title' => $form->data()->title,
            'captcha' => (bool) $form->data()->captcha,
            'comment_status' => $form->data()->comment_status,
            'source' => $form->data()->source,
        ];

        // Form fields
        $fields = [];
        foreach ($form->getFields() as $field) {
            $fields[] = [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'required' => (bool) $field->required,
                'min' => $field->min,
                'max' => $field->max,
                'placeholder' => $field->placeholder,
                'options' => !empty($field->options) ? explode(',', str_replace("\r", "", $field->options)) : [],
                'info' => $field->info,
                'regex' => $field->regex,
                'default_value' => $field->default_value,
            ];
        }
        $return['fields'] = $fields;

        // Form permissions
        $permissions = [];
        $permissions_query = $api->getDb()->query('SELECT * FROM nl2_forms_permissions WHERE form_id = ?', [$form->data()->id])->results();
        foreach ($permissions_query as $permission) {
            $permissions[] = [
                'group_id' => $permission->group_id,
                'post' => (bool) $permission->post,
                'view_own' => (bool) $permission->view_own,
                'delete' => (bool) $permission->can_delete
            ];
        }
        $return['permissions'] = $permissions;

        // Form statuses
        $statuses = [];
        $form_statuses = DB::getInstance()->query('SELECT * FROM nl2_forms_statuses WHERE deleted = 0');
        if ($form_statuses->count()) {
            foreach ($form_statuses->results() as $status_query) {
                $form_ids = explode(',', $status_query->fids);

                if (in_array($form->data()->id, $form_ids) || $status_query->id == 1) {
                    $groups_list = [];
                    $groups = explode(',', $status_query->gids);
                    foreach ($groups as $group) {
                        $groups_list[] = [
                            'group_id' => (int) $group
                        ];
                    }

                    $statuses[] = [
                        'id' => $status_query->id,
                        'html' => Output::getPurified($status_query->html),
                        'permissions' => $groups_list
                    ];
                }
            }
        }
        $return['statuses'] = $statuses;

        $api->returnArray($return);
    }
}