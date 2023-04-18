<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 */

class Submission {

    private DB $_db;
    private $_data;
    private array $_errors = [];

    // Constructor, connect to database
    public function __construct(?string $value = null, ?string $field = 'id', $query_data = null) {
        $this->_db = DB::getInstance();
        
        if (!$query_data && $value) {
            $data = $this->_db->get('forms_replies', [$field, '=', $value]);
            if ($data->count()) {
                $this->_data = $data->first();
            }
        } else if ($query_data) {
            // Load data from existing query.
            $this->_data = $query_data;
        }
    }

    /**
     * Does this submission exist?
     *
     * @return bool Whether the submission exists (has data) or not.
     */
    public function exists(): bool {
        return (!empty($this->_data));
    }

    /**
     * Get the submission data.
     *
     * @return object|null This submission data.
     */
    public function data(): ?object {
        return $this->_data;
    }

    /**
     * Create a new submission.
     *
     * @param Form $form The form this submission was submitted for.
     * @param User|null $user The user who submitted this submission.
     */
    public function create(Form $form, ?User $user, array $fields_values): bool {
        $fields = $form->getFields();
        if (!count($fields)) {
            $this->addError('There are no fields for this form, You need to add fields for this form at StaffCP -> Forms');
            return false;
        }

        $user_id = ($user != null && $user->exists()) ? $user->data()->id : null;

        $this->_db->insert('forms_replies', [
            'form_id' => $form->data()->id,
            'user_id' => $user_id,
            'updated_by' => $user_id,
            'created' => date('U'),
            'updated' => date('U'),
            'content' =>  '',
            'status_id' => $form->data()->source == 'forms' ? 1 : 0
        ]);
        $submission_id = DB::getInstance()->lastId();

        if(!is_dir(ROOT_PATH . '/uploads/forms_submissions'))
            mkdir(ROOT_PATH . '/uploads/forms_submissions');

        try {
            $inserts = [];
            $insert_values = [];
            foreach ($fields as $field) {
                if ($field->type != 10) {
                    // Normal POST value
                    if (isset($fields_values[$field->id])) {
                        $item = $fields_values[$field->id];
                        $inserts[] = '(?,?,?),';

                        $value = (!is_array($item) ? nl2br($item) : implode(', ', $item));

                        $insert_values[] = $submission_id;
                        $insert_values[] = $field->id;
                        $insert_values[] = $value;
                    }
                } else {
                    // File Uploading
                    if (isset($_FILES[$field->id])) {
                        $image = new Bulletproof\Image($_FILES[$field->id]);
                        $image->setSize(1, 2097152); // between 1b and 4mb
                        $image->setDimension(2000, 2000); // 2k x 2k pixel maximum
                        $image->setMime(['jpg', 'png', 'jpeg']);
                        $image->setLocation(join(DIRECTORY_SEPARATOR, [ROOT_PATH, 'uploads', 'forms_submissions']));

                        if ($image->getSize() != 0) {
                            $upload = $image->upload();
                            if ($upload) {
                                $inserts[] = '(?,?,?),';

                                $insert_values[] = $submission_id;
                                $insert_values[] = $field->id;
                                $insert_values[] = $upload->getName() . '.' . $upload->getMime();
                            } else {
                                $this->addError(Output::getClean($field->name) . ': ' . $image["error"]);
                            }
                        }
                    }
                }
            }

            $query = 'INSERT INTO nl2_forms_replies_fields (submission_id, field_id, value) VALUES ';
            $query .= implode('', $inserts);
            DB::getInstance()->query(rtrim($query, ','), $insert_values);
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            DB::getInstance()->delete('forms_replies', ['id', '=', $submission_id]);
            return false;
        }

        $data = $this->_db->get('forms_replies', ['id', '=', $submission_id]);
        if ($data->count()) {
            $this->_data = $data->first();

            EventHandler::executeEvent(new SubmissionCreatedEvent(
                $user,
                $form,
                $this,
                json_decode($form->data()->hooks)
            ));

            // Submit submission to another source?
            if ($form->data()->source != 'forms') {
                $source = Forms::getInstance()->getSubmissionSource($form->data()->source);

                if (!$source->create($form, $this, $user, $fields_values)) {
                    $this->delete();
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Update a submission data in the database.
     *
     * @param array $fields Column names and values to update.
     * @throws Exception
     */
    public function update(array $fields = []): void {
        if (!$this->_db->update('forms_replies', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating submission');
        }

        foreach ($fields as $key => $value) {
            $this->_data->$key = $value;
        }
    }

    /**
     * Get the fields answers for this submission
     *
     * @return array The fields answers for this submission
     */
    public function getFieldsAnswers(): array {
        $answer_array = [];
        if (empty($this->data()->content)) {
            // New fields generation
            $fields = $this->_db->query('SELECT name, value, type, field_id FROM nl2_forms_replies_fields LEFT JOIN nl2_forms_fields ON field_id=nl2_forms_fields.id WHERE submission_id = ?', [$this->data()->id])->results();
            foreach ($fields as $field) {
                $answer_array[] = [
                    'field_id' => (int) Output::getClean($field->field_id),
                    'field_type' => (int) Output::getClean($field->type),
                    'question' => Output::getClean($field->name),
                    'answer' => Output::getPurified(Output::getDecoded($field->value))
                ];
            }

        } else {
            // Legacy fields generation
            $answers = json_decode($this->data()->content, true);
            foreach ($answers as $answer) {
                $question = $this->_db->get('forms_fields', ['id', '=', $answer[0]])->results();
                $answer_array[] = [
                    'field_id' => 1,
                    'field_type' => 1,
                    'question' => Output::getClean($question[0]->name),
                    'answer' => Output::getPurified(Output::getDecoded($answer[1]))
                ];
            }
        }

        return $answer_array;
    }

    /**
     * Get current submission status.
     *
     * @return Status Get current submission status.
     */
    public function getStatus(): Status {
        return new Status($this->data()->status_id);
    }

    /**
     * Add an error to the errors array.
     *
     * @param string $error The error message.
     */
    public function addError(string $error): void {
        $this->_errors[] = $error;
    }

    /**
     * Get any errors from the functions given by this integration.
     *
     * @return array Any errors.
     */
    public function getErrors(): array {
        return $this->_errors;
    }

    /*
     * Delete this submission.
     */
    public function delete(): void {
        $this->_db->delete('forms_replies', ['id', '=', $this->data()->id]);
        $this->_db->delete('forms_replies_fields', ['submission_id', '=', $this->data()->id]);
        $this->_db->delete('forms_comments', ['form_id', '=', $this->data()->id]);
    }
}