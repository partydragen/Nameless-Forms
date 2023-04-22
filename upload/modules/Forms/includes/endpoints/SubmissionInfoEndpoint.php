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
        $status = $submission->getStatus();

        if ($submission->data()->user_id != null) {
            $user = new User($submission->data()->user_id);
            if ($user->exists()) {
                $by_username = $user->getDisplayname(true);
            } else {
                $by_username = $api->getLanguage()->get('general', 'deleted_user');
            }
        }

        if ($submission->data()->updated_by != null) {
            $updated_by = new User($submission->data()->updated_by);
            if ($updated_by->exists()) {
                $updated_by_username = $updated_by->getDisplayname(true);
            } else {
                $updated_by_username = $api->getLanguage()->get('general', 'deleted_user');
            }
        }

        $return = [
            'id' => $submission->data()->id,
            'form' => [
                'id' => $submission->data()->form_id,
                'title' => $form->data()->title
            ],
            'submitter' => $submission->data()->user_id ? [
                'id' => $submission->data()->user_id,
                'username' => $by_username,
            ] : null,
            'updated_by_user' => $submission->data()->updated_by != null ? [
                'id' => $submission->data()->updated_by,
                'username' => $updated_by_username,
            ] : null,
            'status' => [
                'id' => $submission->data()->status_id,
                'name' => strip_tags($status->data()->html),
                'open' => $status->data()->open,
            ],
            'created' => $submission->data()->created,
            'last_updated' => $submission->data()->updated,
            'source' => $submission->data()->source,
            'fields' => $submission->getFieldsAnswers(),
            'url' => URL::getSelfURL() . ltrim(URL::build('/panel/forms/submissions/', 'view=' . $submission->data()->id), '/')
        ];

        $api->returnArray($return);
    }
}