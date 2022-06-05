<?php
class FormInfoEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/{form}';
        $this->_module = 'Forms';
        $this->_description = 'Get form details';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api, Form $form): void {
        $return = [
            'id' => $form->data()->id,
            'url' => Output::getClean($form->data()->url),
            'url_full' => rtrim(Util::getSelfURL(), '/') . URL::build($form->data()->url),
            'title' => Output::getClean($form->data()->title),
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