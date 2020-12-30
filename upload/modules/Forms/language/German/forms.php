<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  German Language for Forms module
 */

$language = array(
	// Forms
	'forms' => 'Formulare',
	'form' => 'Abgesendet',
	'new_form' => 'Neues Formular',
	'form_name' => 'Formularname',
	'form_url' => 'Formular URL (Bspw. /apply)',
	'form_icon' => 'Formular Icon',
	'link_location' => 'Link Position',
	'creating_new_form' => 'Neues Formular erstellen',
	'form_created_successfully' => 'Formular erfolgreich erstellt',
	'none_forms_defined' => 'Es gibt zur Zeit keine Formulare.',
	'delete_form' => 'Bist du sicher darüber, dieses Formular zu löschen?</br>Warnung: Alle Daten, welche mit diesem Formular zusammenhängen werden gelöscht',
	'form_submitted' => 'Formular erfolgreich gesendet',
	'action' => 'Aktion',
	'actions' => 'Aktionen',
	'guest' => 'Gast',
	'sort' => 'Sortieren',
	
	// Permissions
	'forms_view_submissions' => 'AdminCP &raquo; Formulare &raquo; Formulare',
	'forms_manage' => 'AdminCP &raquo; Formulare &raquo; Abgesendet',
	
	// Form
	'editing_x' => 'Ändere {x}', // Don't replace {x}
	'form_created_successfully' => 'Formular erfolgreich erstellt.',
	'form_updated_successfully' => 'Formular erfolgreich geändert.',
	'form_deleted_successfully' => 'Formular erfolgreich gelöscht.',
	'allow_guests' => 'Können Gäste hier einen Formular erstellen?',
	'allow_guests_help' => 'Gäste können Beiträge einreichen, ohne eingeloggt zu sein. Bitte beachten Sie, dass sie die Beiträge anschließend nicht mehr sehen können.',
	'can_user_view' => 'Kann ein Mitglied seinen Formular sehen?',
	'can_user_view_help' => 'Der Benutzer kann seine eigenen Beiträge anzeigen und den Kommentarbereich verwenden. Außerdem erhält er Aktualisierungsbenachrichtigungen, wenn sich der Status ändert oder wenn jemand einen Kommentar abgibt, Bitte beachten Sie, dass dies nicht für Gäste funktioniert.',
	
	// Fields
	'field' => 'Feld',
	'fields' => 'Felder',
	'new_field' => 'Neues Feld',
	'field_name' => 'Feldname',
	'field_created_successfully' => 'Feld erfolgreich erstellt',
	'field_updated_successfully' => 'Feld erfolgreich geändert',
	'field_deleted_successfully' => 'Feld erfolgreich gelöscht',
	'new_field_for_x' => 'Erstelle ein neues Feld für {x}',
	'editing_field_for_x' => 'Ändere neues Feld für {x}',
	'none_fields_defined' => 'Es gibt zur Zeit keine Felder.',
	'confirm_delete_field' => 'Bist du sicher, dieses Feld zu löschen?',
	'options' => 'Optionen',
	'options_help' => 'Jede Option in einer neuen Zeile; kann leer gelassen werden (Nur bei Optionen)',
	'field_order' => 'Reihenfolge',
	'help_box' => 'Help Text',
	'barrier' => 'Dividing Line',
	'delete_field' => 'Bist du sicher, dass du dieses Feld löschen möchtest?',
	
	// Statuses
	'statuses' => 'Status',
	'status' => 'Status',
	'new_status' => 'Neuer Status',
	'creating_status' => 'Erstelle einen neuen Status',
	'editing_status' => 'Ändere einen Status',
	'marked_as_open' => 'Als offen markiert',
	'status_name' => 'Status Name',
	'status_html' => 'Status HTML',
	'status_forms' => 'Select forms where this status will be displayed on. (Ctrl+click to select/deselect multiple)',
	'status_groups' => 'Select groups who are allowed to select this status. (Ctrl+click to select/deselect multiple)',
	'status_creation_success' => 'Status erfolgreich erstellt.',
	'status_creation_error' => 'Es ist ein Fehler aufgetreten! Bitte überprüfe, dass die HTML nicht mehr als 1024 Zeichen enthält.',
	'status_edit_success' => 'Status erfolgreich geändert.',
	'status_deleted_successfully' => 'Status erfolgreich gelöscht.',
	'delete_status' => 'Bist du dir sicher, diesen Status zu löschen??',

	// Errors
	'input_form_name' => 'Bitte füge einen Formularnamen hinzu.',
	'input_form_url' => 'Bitte füge eine Formulars-URL hinzu.',
	'form_name_minimum' => 'Der Formularname muss aus mindestens 2 Zeichen bestehen.',
	'form_url_minimum' => 'Die Formular-URL muss aus mindestens 2 Zeichen bestehen.',
	'form_name_maximum' => 'Der Formularname darf aus maximal 32 Zeichen bestehen.',
	'form_url_maximum' => 'Die Formular-URL darf aus maximal 32 Zeichen bestehen.',
	'form_icon_maximum' => 'Das Formular-Icon darf aus maximal 64 Zeichen bestehen.',
	'input_field_name' => 'Bitte füge einen Feldnamen hinzu.',
	'field_name_minimum' => 'Der Feldname muss mindestens 2 Zeichen lang sein.',
	'field_name_maximum' => 'Der Feldname darf aus maximal 255 Zeichen bestehen.',
	
	// Submissions
	'submissions' => 'Formulare',
	'submission_updated' => 'Formular erfolgreich geändert',
	'no_open_submissions' => 'Es gibt zur Zeit keine offenen Formulare.',
	'no_closed_submissions' => 'Es gibt zur Zeit keine geschlossenen Formulare.',
	'form_x' => 'Formular: {x}',
	'current_status_x' => 'Aktueller Status: {x}',
	'last_updated' => 'Letztes Update von:',
	'your_submission_updated' => 'Dein Formular wurde verändert!',
	'user' => 'Benutzer',
	'updated_by' => 'Geändert von',
	'sort' => 'Sort',
	
	// Update alerts
	'new_update_available_x' => 'There is a new update available for the module {x}',
	'new_urgent_update_available_x' => 'There is a new urgent update available for the module {x}. Please update as soon as possible!',
	'current_version_x' => 'Current module version: {x}',
	'new_version_x' => 'New module version: {x}',
	'view_resource' => 'View Resource',
    
    // Hook
    'new_form_submission' => 'New form submission',
    'updated_form_submission' => 'New form submission comment',
    'new_submission_text' => 'New submission created in {x} by {y}',
    'updated_submission_text' => 'New submission comment in {x} by {y}'
    //'updated_submission_text' => 'Submission updated in {x} by {y}'
);
