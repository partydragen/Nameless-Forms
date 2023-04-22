<?php
class ListSubmissionsEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/submissions';
        $this->_module = 'Forms';
        $this->_description = 'List all submissions';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api): void {
        $query = 'SELECT * FROM nl2_forms_replies';
        $where = '';
        $order = ' ORDER BY `created` DESC';
        $limit = '';
        $params = [];

        // Get submissions submitted to source
        if (isset($_GET['source']) && is_numeric($_GET['source'])) {
            $where .= ' AND `source` = ?';
            $params[] = $_GET['status'];
        } else {
            $where .= ' WHERE source IS NULL';
        }

        // Get submissions from a specific form.
        if (isset($_GET['form']) && is_numeric($_GET['form'])) {
            $where .= ' AND `form_id` = ?';
            $params[] = $_GET['form'];
        }

        // Get submissions submitted from user.
        if (isset($_GET['user']) && is_numeric($_GET['user'])) {
            $where .= ' AND `user_id` = ?';
            $params[] = $_GET['user'];
        }

        // Get submissions updated by certain user.
        if (isset($_GET['updated_by_user']) && is_numeric($_GET['updated_by_user'])) {
            $where .= ' AND `updated_by` = ?';
            $params[] = $_GET['updated_by_user'];
        }

        // Get submissions from certain status
        if (isset($_GET['status']) && is_numeric($_GET['status'])) {
            $where .= ' AND `status_id` = ?';
            $params[] = $_GET['status'];
        }

        // Limit the amount of submissions returned
        if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
            $limit .= ' LIMIT '. $_GET['limit'];
        }

        $submissions_list = [];
        $submissions_query = $api->getDb()->query($query . $where . $order . $limit, $params)->results();
        foreach ($submissions_query as $submission) {
            $submissions_list[] = [
                'id' => $submission->id,
                'form_id' => $submission->form_id,
                'user_id' => $submission->user_id,
                'updated_by' => $submission->updated_by,
                'created' => $submission->created,
                'last_updated' => $submission->updated,
                'status_id' => $submission->status_id
            ];
        }

        $api->returnArray(['submissions' => $submissions_list]);
    }
}