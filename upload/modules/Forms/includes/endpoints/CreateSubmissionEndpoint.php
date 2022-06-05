<?php
class CreateSubmissionEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/{form}/submissions/create';
        $this->_module = 'Forms';
        $this->_description = 'Create a new form submission';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api, Form $form): void {
        $api->validateParams($_POST, ['field_values']);

        $user != null;
        if (isset($_POST['user'])) {
            $user = $this::transformUser($api, $_POST['user']);
        }

        $validation = $form->validateFields($_POST['field_values'], Forms::getLanguage(), $api->getLanguage());
        if (!$validation->passed()) {
            // Validation errors
            $api->throwError(FormsApiErrors::ERROR_VALIDATION_ERRORS, $validation->errors());
        }

        $submission = new Submission();
        if (!$submission->create($form, $user, $_POST['field_values'])) {
            $api->throwError(FormsApiErrors::ERROR_UNKNOWN_ERROR, $submission->getErrors());
        }

        $api->returnArray([
            'submission_id' => $submission->data()->id,
            'link' => rtrim(Util::getSelfURL(), '/') . URL::build('/user/submissions/', 'view=' . Output::getClean($submission->data()->id))
        ]);
    }

    private function transformUser(Nameless2API $api, string $value) {
        return Endpoints::getAllTransformers()['user']['transformer']($api, $value);
    }
}