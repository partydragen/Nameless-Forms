<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.1.0
 *
 *  License: MIT
 */

class Form {

    private $_db,
            $_data,
            $_fields;

    // Constructor, connect to database
    public function __construct(string $value = null, string $field = 'id') {
        $this->_db = DB::getInstance();
        
        if ($value != null) {
            $data = $this->_db->get('forms', [$field, '=', $value]);
            if ($data->count()) {
                $this->_data = $data->first();
            }
        }
    }

    /*
     * Does this form exist?
     *
     * @return bool Whether the order exists (has data) or not.
     */
    public function exists(): bool {
        return (!empty($this->_data));
    }

    /*
     * Get the form data.
     *
     * @return object|null This form data.
     */
    public function data(): ?object {
        return $this->_data;
    }

    /*
     * Update a form data in the database.
     *
     * @param array $fields Column names and values to update.
     */
    public function update(array $fields = []): void {
        if (!$this->_db->update('forms', $this->data()->id, $fields)) {
            throw new Exception('There was a problem updating form');
        }
    }

    /*
     * Get the form fields.
     *
     * @return array Their fields.
     */
    public function getFields(): array {
        if ($this->_fields == null) {
            $this->_fields = [];
            
            $fields_query = $this->_db->query('SELECT * FROM nl2_forms_fields WHERE form_id = ? AND deleted = 0 ORDER BY `order`', [$this->data()->id]);
            if ($fields_query->count()) {
                $fields_query = $fields_query->results();
                foreach ($fields_query as $field) {
                    $this->_fields[$field->id] = $field;
                }
            }
        }

        return $this->_fields;
    }

    /*
     * Validate all fields values
     *
     * @return Validate
     */
    public function validateFields(array $field_values, Language $forms_language, Language $language): Validate {
        $to_validate = [];
        $to_validate_messages = [];

        $to_validate['token'] = [Validate::RATE_LIMIT => [1, 5]];
        $to_validate_messages['token'] = [Validate::RATE_LIMIT => $forms_language->get('forms', 'post_rate_limit')];

        foreach ($this->getFields() as $field) {
            $field_validation = [];
            $field_validation_message = [];

            if ($field->required == 1) {
                $field_validation[Validate::REQUIRED] = true;
                $field_validation_message[Validate::REQUIRED] = $language->get('user', 'field_is_required', ['field' => Output::getClean($field->name)]);
            }

            if ($field->min != 0) {
                $field_validation[Validate::MIN] = $field->min;
                $field_validation_message[Validate::MIN] = $forms_language->get('forms', 'x_field_minimum_y', ['field' => Output::getClean($field->name), 'min' => $field->min]);
            }

            if ($field->max != 0) {
                $field_validation[Validate::MAX] = $field->max;
                $field_validation_message[Validate::MAX] = $forms_language->get('forms', 'x_field_maximum_y', ['field' => Output::getClean($field->name), 'max' => $field->max]);
            }

            if ($field->regex != null) {
                $field_validation[Validate::REGEX] = $field->regex;
                $field_validation_message[Validate::REGEX] = $forms_language->get('forms', 'x_field_regex', ['field' => Output::getClean($field->name)]);
            }

            if (count($field_validation)) {
                $to_validate[$field->id] = $field_validation;
                $to_validate_messages[$field->id] = $field_validation_message;
            }
        }

        // Modify post validation
        $validate_post = [];
        foreach ($field_values as $key => $item) {
            $validate_post[$key] = !is_array($item) ? $item : true;
        }
            
        foreach ($_FILES as $key => $item) {
            $validate_post[$key] = $item['tmp_name'];
        }
        
        return Validate::check($validate_post, $to_validate)->messages($to_validate_messages);
    }
    
    /*
     * Delete everything related to this form.
     */
    public function delete(): void {
        $this->_db->delete('forms', ['id', '=', $this->data()->id]);
        $this->_db->delete('forms_permissions', ['form_id', '=', $this->data()->id]);
        $this->_db->delete('forms_fields', ['form_id', '=', $this->data()->id]);
        $this->_db->delete('forms_replies', ['form_id', '=', $this->data()->id]);
        $this->_db->delete('forms_comments', ['form_id', '=', $this->data()->id]);
    }
}