<?php
class FormInfoEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/form';
        $this->_module = 'Forms';
        $this->_description = 'Get form details';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api): void {
        $api->validateParams($_GET, ['form']);

        if (is_numeric($_GET['form'])) {
            // Get form by id
            $form = new Form($_GET['form']);
        } else {
            // Get form by url
            $form = new Form('/' . $_GET['form'], 'url');
        }

        if (!$form->exists()) {
            $api->throwError(FormsApiErrors::ERROR_FORM_NOT_FOUND);
        }

        $return = [
            'id' => $form->data()->id,
            'url' => Output::getClean($form->data()->url),
            'title' => Output::getClean($form->data()->title),
            'guest' => (bool) $form->data()->guest,
            'can_view' => (bool) $form->data()->can_view,
            'captcha' => (bool) $form->data()->captcha,
            'comment_status' => $form->data()->comment_status,
        ];

        $fields = [];
        foreach ($form->getFields() as $field) {
            $fields[] = [
                'id' => $field->id,
                'name' => Output::getClean($field->name),
                'type' => $field->type,
                'required' => (bool) $field->required,
                'min' => $field->min,
                'max' => $field->max,
                'placeholder' => Output::getClean($field->placeholder),
                'options' => $field->options,
                'info' => Output::getClean($field->info)
            ];
        }
        $return['fields'] = $fields;

        $api->returnArray($return);
    }
}