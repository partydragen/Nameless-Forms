<?php
class SubmissionUpdatedStaffEvent extends AbstractEvent implements HasWebhookParams, DiscordDispatchable {
    public User $user;
    public Submission $submission;
    public string $content;
    public bool $anonymous;
    public bool $staff_only;
    public ?array $available_hooks;

    public function __construct(User $user, Submission $submission, string $content, bool $anonymous, bool $staff_only, ?array $available_hooks) {
        $this->user = $user;
        $this->submission = $submission;
        $this->content = $content;
        $this->anonymous = $anonymous;
        $this->staff_only = $staff_only;
        $this->available_hooks = $available_hooks;
    }

    public static function name(): string {
        return 'updatedFormSubmissionStaff';
    }

    public static function description(): string {
        return (new Language(ROOT_PATH . '/modules/Forms/language'))->get('forms', 'updated_form_submission_staff');
    }

    public function webhookParams(): array {
        $form = new form($this->submission->data()->form_id);
        $submitter = $this->submission->data()->user_id != null ? new User($this->submission->data()->user_id) : null;
        $status = $this->submission->getStatus();

        return [
            'id' => $this->submission->data()->id,
            'form' => [
                'id' => $form->data()->id,
                'title' => $form->data()->title
            ],
            'submitter' => $submitter != null && $submitter->exists() ? [
                'id' => $submitter->data()->id,
                'username' => $submitter->getDisplayname(),
                'avatar' => $submitter->getAvatar(128, true)
            ] : null,
            'updated_by_user' => [
                'id' => $this->user->data()->id,
                'username' => $this->user->getDisplayname(),
                'avatar' => $this->user->getAvatar(128, true),
                'content' => $this->content,
                'anonymous' => $this->anonymous,
                'staff_only' => $this->staff_only,
            ],
            'status' => [
                'id' => $this->submission->data()->status_id,
                'name' => strip_tags($status->data()->html),
                'open' => $status->data()->open,
            ],
            'created' => $this->submission->data()->created,
            'last_updated' => $this->submission->data()->updated,
            'fields' => $this->submission->getFieldsAnswers(),
            'url' => URL::getSelfURL() . ltrim(URL::build('/panel/forms/submissions/', 'view=' . $this->submission->data()->id), '/')
        ];
    }

    public function toDiscordWebhook(): DiscordWebhookBuilder {
        $language = new Language(ROOT_PATH . '/modules/Forms/language', DEFAULT_LANGUAGE);
        $form = new form($this->submission->data()->form_id);

        return DiscordWebhookBuilder::make()
            ->setUsername($form->data()->title)
            ->setAvatarUrl($this->user->getAvatar(128, true))
            ->addEmbed(function (DiscordEmbed $embed) use ($language, $form) {
                return $embed
                    ->setTitle('[#' . $this->submission->data()->id . '] ' . $form->data()->title)
                    ->setDescription(Text::embedSafe($this->content))
                    ->setFooter($language->get('forms', 'updated_submission_text', [
                        'form' => $form->data()->title,
                        'user' => $this->user->getDisplayname()
                    ]))
                    ->setUrl(URL::getSelfURL() . ltrim(URL::build('/panel/forms/submissions/', 'view=' . $this->submission->data()->id), '/'))
                    ->setColor($this->submission->getStatus()->data()->color);
            });
    }

}