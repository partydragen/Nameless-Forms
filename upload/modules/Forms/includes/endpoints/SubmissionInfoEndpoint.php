<?php
class SubmissionInfoEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/submissions/{submission}';
        $this->_module = 'Forms';
        $this->_description = 'Get submission info';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api, Submission $submission): void {
        $form = new Form($submission->data()->form_id);
        $status = new Status($submission->data()->status_id);

        $by_username = Forms::getLanguage()->get('forms', 'guest');
        if ($submission->data()->user_id != null) {
            $user = new User($submission->data()->user_id);
            if (!$user->exists()) {
                $by_username = $api->getLanguage()->get('general', 'deleted_user');
            }
            $by_username = $user->getDisplayname(true);
        }

        $updated_by_username = Forms::getLanguage()->get('forms', 'guest');
        if ($submission->data()->updated_by != null) {
            $updated_by = new User($submission->data()->updated_by);
            if (!$updated_by->exists()) {
                $updated_by_username = $api->getLanguage()->get('general', 'deleted_user');
            }
            $updated_by_username = $updated_by->getDisplayname(true);
        }

        $return = [
            'id' => $submission->data()->id,
            'form' => [
                'id' => $submission->data()->form_id,
                'title' => $form->data()->title
            ],
            'user' => [
                'id' => $submission->data()->user_id,
                'username' => $by_username,
            ],
            'updated_by_user' => [
                'id' => $submission->data()->updated_by,
                'username' => $updated_by_username,
            ],
            'created' => $submission->data()->created,
            'last_updated' => $submission->data()->updated,
            'status' => [
                'id' => $submission->data()->status_id,
                'name' => strip_tags($status->data()->html),
                'open' => $status->data()->open,
            ],
            'fields' => $submission->getFieldsAnswers()
        ];

        $api->returnArray($return);
    }
}