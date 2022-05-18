<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 */

class Status {

    private DB $_db;

    // Constructor, connect to database
    public function __construct(string $value = null, string $field = 'id') {
        $this->_db = DB::getInstance();
        
        if ($value != null) {
            $data = $this->_db->get('forms_statuses', [$field, '=', $value]);
            if ($data->count()) {
                $this->_data = $data->first();
            }
        }
    }

    /**
     * Does this status exist?
     *
     * @return bool Whether the status exists (has data) or not.
     */
    public function exists(): bool {
        return (!empty($this->_data));
    }

    /**
     * Get the status data.
     *
     * @return object This status data.
     */
    public function data(): ?object {
        return $this->_data;
    }

    /**
     * Update a status data in the database.
     *
     * @param array $fields Column names and values to update.
     */
    public function update(array $fields = []): void {
        if (!$this->_db->update('forms_statuses', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating status');
        }
    }

    /**
     * Mark this status as deleted
     */
    public function delete(): void {
        $this->_db->update('forms_statuses', $this->data()->id, [
            'deleted' => 1
        ]);
    }
}