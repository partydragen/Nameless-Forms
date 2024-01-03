<?php
class UpdateSubmissionEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/submissions/{submission}/update';
        $this->_module = 'Suggestions';
        $this->_description = 'Update submission';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api, Submission $submission): void {
        $api->validateParams($_POST, ['user']);

        $user = $this::transformUser($api, $_POST['user']);
        $update_array = [];

        // Update submission status?
        if (isset($_POST['status']) && $submission->data()->status_id != $_POST['status']) {
            $new_status = new Status($_POST['status']);
            if ($new_status->exists()) {
                $groups = explode(',', $new_status->data()->gids);
                $hasperm = false;
                foreach ($user->getAllGroupIds() as $group_id) {
                    if (in_array($group_id, $groups)) {
                        $hasperm = true;
                        break;
                    }
                }

                if ($hasperm) {
                    $update_array['status_id'] = $new_status->data()->id;
                } else {
                    // No permission to use this status
                    $api->throwError(FormsApiErrors::ERROR_CANNOT_CHANGE_STATUS);
                }
            }
        }

        // Update updated by
        if (isset($_POST['updated_by']) && is_numeric($_POST['updated_by'])) {
            $update_array['updated_by'] = $_POST['updated_by'];
        }

        if (count($update_array)) {
            $update_array['updated_by'] = $update_array['updated_by'] ?? $user->data()->id;
            $update_array['updated'] = date('U');
            $submission->update($update_array);

            $api->returnArray(['success' => true, 'updated' => $update_array]);
        } else {
            $api->returnArray(['success' => false]);
        }
    }

    private function transformUser(Nameless2API $api, string $value) {
        return Endpoints::getAllTransformers()['user']['transformer']($api, $value);
    }
}