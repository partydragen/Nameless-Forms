<?php
class CommentSubmissionEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'forms/submissions/{submission}';
        $this->_module = 'Suggestions';
        $this->_description = 'Leave a comment on the suggestion';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api, Submission $submission): void {
        $api->validateParams($_POST, ['user', 'content']);

        $user = $this::transformUser($api, $_POST['user']);
        $anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == true;
        $staff_only = isset($_POST['staff_only']) && $_POST['staff_only'] == true;
        $form = new Form($submission->data()->form_id);

        $submission->update([
            'updated_by' => ($anonymous != 1 ? $user->data()->id : 0),
            'updated' => date('U')
        ]);

        DB::getInstance()->insert('forms_comments', [
            'form_id' => $submission->data()->id,
            'user_id' => $user->data()->id,
            'created' => date('U'),
            'anonymous' => $anonymous,
            'content' => nl2br(Input::get('content')),
            'staff_only' => $staff_only
        ]);
        $comment_id = $api->getDb()->lastId();

        EventHandler::executeEvent(new SubmissionUpdatedStaffEvent(
            $user,
            $submission,
            '',
            $anonymous,
            $staff_only,
            json_decode($form->data()->hooks)
        ));

        $api->returnArray(['comment_id' => (int)$comment_id]);
    }

    private function transformUser(Nameless2API $api, string $value) {
        return Endpoints::getAllTransformers()['user']['transformer']($api, $value);
    }
}