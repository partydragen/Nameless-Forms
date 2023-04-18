<?php

abstract class SubmissionBase {

    abstract public function getName(): string;

    abstract public function create(Form $form, Submission $submission, User $user, array $fields_values): bool;

    abstract public function getURL(Submission $submission): string;
}