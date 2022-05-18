<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 */

class Submission {

    private DB $_db;

    // Constructor, connect to database
    public function __construct(string $value = null, string $field = 'id') {
        $this->_db = DB::getInstance();
        
        if ($value != null) {
            $data = $this->_db->get('forms_replies', [$field, '=', $value]);
            if ($data->count()) {
                $this->_data = $data->first();
            }
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
     * @return object This submission data.
     */
    public function data(): ?object {
        return $this->_data;
    }

    /**
     * Update a submission data in the database.
     *
     * @param array $fields Column names and values to update.
     */
    public function update(array $fields = []): void {
        if (!$this->_db->update('forms_replies', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating submission');
        }
    }
    
    public function delete(): void {
        $this->_db->delete('forms_replies', ['id', '=', $this->data()->id]);
        $this->_db->delete('forms_replies_fields', ['submission_id', '=', $this->data()->id]);
        $this->_db->delete('forms_comments', ['form_id', '=', $this->data()->id]);
    }
}