<?php
class ForumSubmissionSource extends SubmissionBase {

    public function getName(): string {
        return 'Forum';
    }

    public function create(Form $form, Submission $submission, User $user, array $fields_values): bool {
        $forum = DB::getInstance()->get('forums', ['id', '=', $form->data()->forum_id]);
        if (!$forum->count()) {
            $submission->addError('Forum id '.$form->data()->forum_id.' not found, Please contact a administrator');
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

        $title = '[#' . $submission->data()->id . '] ' . $form->data()->title;
        DB::getInstance()->insert('topics', [
            'forum_id' => $form->data()->forum_id,
            'topic_title' => $title,
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

        $available_hooks = json_decode($forum->hooks) ?? [];
        EventHandler::executeEvent(new TopicCreatedEvent(
            $user,
            $forum->forum_title,
            $title,
            $content,
            $topic_id,
            $available_hooks,
        ));

        $submission->update([
            'source' => 'forum',
            'source_id' => $topic_id
        ]);

        Session::flash('success_post', Forms::getLanguage()->get('forms', 'form_submitted'));
        return true;
    }

    public function getURL(Submission $submission): string {
        return URL::build('/forum/topic/' . $submission->data()->source_id);
    }
}