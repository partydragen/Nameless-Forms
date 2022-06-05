<?php
class ListFormsEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms';
        $this->_module = 'Forms';
        $this->_description = 'List all forms';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api): void {
        $forms_list = [];
        $forms_query = $api->getDb()->query('SELECT * FROM nl2_forms')->results();
        foreach($forms_query as $form) {
            $forms_list[] = [
                'id' => $form->id,
                'url' => $form->url,
                'url_full' => rtrim(Util::getSelfURL(), '/') . URL::build($form->url),
                'title' => $form->title,
                'captcha' => (bool) $form->captcha,
                'comment_status' => $form->comment_status,
            ];
        }

        $api->returnArray(['forms' => $forms_list]);
    }
}