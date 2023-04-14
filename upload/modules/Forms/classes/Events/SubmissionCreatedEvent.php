<?php

class SubmissionCreatedEvent extends AbstractEvent implements HasWebhookParams, DiscordDispatchable {
    public ?User $user;
    public Form $form;
    public Submission $submission;
    public ?array $available_hooks;

    public function __construct(?User $user, Form $form, Submission $submission, ?array $available_hooks) {
        $this->user = $user;
        $this->form = $form;
        $this->submission = $submission;
        $this->available_hooks = $available_hooks;
    }

    public static function name(): string {
        return 'newFormSubmission';
    }

    public static function description(): string {
        return (new Language(ROOT_PATH . '/modules/Forms/language'))->get('forms', 'new_form_submission');
    }

    public function webhookParams(): array {
        $status = $this->submission->getStatus();

        return [
            'id' => $this->submission->data()->id,
            'form' => [
                'id' => $this->form->data()->id,
                'title' => $this->form->data()->title
            ],
            'submitter' => $this->user != null && $this->user->exists() ? [
                'id' => $this->user->data()->id,
                'username' => $this->user->getDisplayname(),
                'avatar' => $this->user->getAvatar(128, true)
            ] : null,
            'updated_by_user' => $this->user != null && $this->user->exists() ? [
                'id' => $this->user->data()->id,
                'username' => $this->user->getDisplayname(),
                'avatar' => $this->user->getAvatar(128, true)
            ] : null,
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

        return DiscordWebhookBuilder::make()
            ->setUsername($this->form->data()->title)
            ->setAvatarUrl($this->user != null && $this->user->exists() ? $this->user->getAvatar(128, true) : null)
            ->addEmbed(function (DiscordEmbed $embed) use ($language) {
                return $embed
                    ->setTitle('[#' . $this->submission->data()->id . '] ' . $this->form->data()->title)
                    ->setFooter($language->get('forms', 'new_submission_text', [
                        'form' => $this->form->data()->title,
                        'user' => ($this->user != null && $this->user->exists() ? $this->user->getDisplayname() : Forms::getLanguage()->get('forms', 'guest'))
                    ]))
                    ->setUrl(URL::getSelfURL() . ltrim(URL::build('/panel/forms/submissions/', 'view=' . $this->submission->data()->id), '/'))
                    ->setColor($this->submission->getStatus()->data()->color);
            });
    }

}