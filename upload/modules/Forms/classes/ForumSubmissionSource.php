<?php
class ForumSubmissionSource {

    public function create(Form $form, Submission $submission, array $fields_values): void {
        $forum = $this->_db->get('forums', ['id', '=', $form->data()->forum_id]);
        if (!$forum->count()) {
            $this->addError('Forum id '.$form->data()->forum_id.' not found, Please contact a administrator');
            return false;
        }
        $forum = $forum->first();

        $content = '';
        foreach ($form->getFields() as $field) {
            if (isset($fields_values[$field->id])) {
                $item = $fields_values[$field->id];

                $content .= '<strong>' . $field->name . '</strong><br />';
                $content .= (!is_array($item) ? nl2br($item) : implode(', ', str_ireplace("\r", "", $item))) . '<br />';
                $content .= '<br />';
            }
        }

        DB::getInstance()->insert('topics', [
            'forum_id' => $form->data()->forum_id,
            'topic_title' => $form->data()->title,
            'topic_creator' => $user->data()->id,
            'topic_last_user' => $user->data()->id,
            'topic_date' => date('U'),
            'topic_reply_date' => date('U')
        ]);
        $topic_id = DB::getInstance()->lastId();

        DB::getInstance()->insert('posts', [
            'forum_id' => $form->data()->forum_id,
            'topic_id' => $topic_id,
            'post_creator' => $user->data()->id,
            'post_content' => $content,
            'post_date' => date('Y-m-d H:i:s'),
            'created' => date('U')
        ]);

        // Get last post ID
        $last_post_id = DB::getInstance()->lastId();
        $content = EventHandler::executeEvent('preTopicCreate', [
            'content' => $content,
            'post_id' => $last_post_id,
            'topic_id' => $topic_id,
            'user' => $user,
        ])['content'];

        DB::getInstance()->update('posts', $last_post_id, [
            'post_content' => $content
        ]);

        DB::getInstance()->update('forums', $form->data()->forum_id, [
            'last_post_date' => date('U'),
            'last_user_posted' => $user->data()->id,
            'last_topic_posted' => $topic_id
        ]);

        $title = $form->data()->title;
        $available_hooks = json_decode($forum->hooks) ?? [];
        EventHandler::executeEvent(new TopicCreatedEvent(
            $user,
            $forum->forum_title,
            $title,
            $content,
            $topic_id,
            $available_hooks,
        ));

        Session::flash('success_post', Forms::getLanguage()->get('forms', 'form_submitted'));
        Redirect::to(URL::build('/forum/topic/' . $topic_id));
        return true;
    }
}