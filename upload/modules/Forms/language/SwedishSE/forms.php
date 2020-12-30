<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  SwedishSE Language for Forms module by Hyfalls
 */

$language = array(
	// Forms
	'forms' => 'Formulär',
	'form' => 'Formulär',
	'new_form' => 'Nytt formulär',
	'form_name' => 'Formulärets Rubrik',
	'form_url' => 'Formulärets URL (med föregående /, t.ex /exempel)',
	'form_icon' => 'Formulärets Ikon',
	'link_location' => 'Länk Plats',
	'creating_new_form' => 'Skapar nytt formulär',
	'form_created_successfully' => 'Form created successfully',
	'none_forms_defined' => 'Det finns inga formulär än.',
	'delete_form' => 'Är du säker på att du vill radera det här formuläret?</br>Varning: Varning: Alla uppgifter som hör till detta formulär raderas (frågor och inlägg)',
	'form_submitted' => 'Formulär skickad',
	'action' => 'Action',
	'actions' => 'Actions',
	'guest' => 'Gäst',
	
	// Form
	'editing_x' => 'Redigerar {x}', // Don't replace {x}
	'form_created_successfully' => 'Formuläret har skapats',
	'form_updated_successfully' => 'Formuläret har uppdaterats.',
	'form_deleted_successfully' => 'Formuläret har raderats.',
	'allow_guests' => 'Kan gäster visa detta formulär?',
	'allow_guests_help' => 'Guests will be able to send in submissions without being logged in, Please note them won\'t be able to view the submission afterwards',
	'can_user_view' => 'Can user view his own submission?',
	'can_user_view_help' => 'User will be able to view his own submission and use the comment section, User will also receive update alerts when the status change or when someone comment, Please note this won\'t work for guests.',
	
	// Fields
	'field' => 'Fält',
	'fields' => 'Fält',
	'new_field' => 'Nytt Fält',
	'field_name' => 'Fält namn',
	'field_created_successfully' => 'Fältet har skapats',
	'field_updated_successfully' => 'Fältet har uppdaterats',
	'field_deleted_successfully' => 'Fältet har raderats',
	'new_field_for_x' => 'Skapar nytt fält för {x}',
	'editing_field_for_x' => 'Redigerar fält för{x}',
	'none_fields_defined' => 'Det finns inga fält än',
	'confirm_delete_field' => 'Är du säker på att du vill radera detta fält?',
	'options' => 'Inställningar',
	'options_help' => 'Varje alternativ på en ny linje; kan lämnas tomt (endast alternativ). Hjälptexter bör också sättas i detta fältet',
	'field_order' => 'Fältorder',
	'delete_field' => 'Är du säker på att du vill radera detta fält?',
    'help_box' => 'Hjälp text',
    'barrier' => 'Skiljelinje',
    'number' => 'Number',
	
	// Statuses
	'statuses' => 'statusar',
	'status' => 'Status',
	'new_status' => 'Ny Status',
	'creating_status' => 'Skapar ny status',
	'editing_status' => 'Redigerar status',
	'marked_as_open' => 'Markerad som öppen',
	'status_name' => 'Status Namn',
	'status_html' => 'Status HTML',
	'status_forms' => 'Select forms where this status will be displayed on. (Ctrl+click to select/deselect multiple)',
	'status_groups' => 'Select groups who are allowed to select this status. (Ctrl+click to select/deselect multiple)',
	'status_creation_success' => 'Status har skapats.',
	'status_creation_error' => 'Ett fel uppstod vid att skapa en status. Se till att statusets html inte är längre än 1024 tecken.',
	'status_edit_success' => 'Status har uppdaterats.',
	'status_deleted_successfully' => 'Status deleted successfully.',
	'delete_status' => 'Är du säker på att du vill radera detta status?',

	// Errors
	'input_form_name' => 'Vänligen ange ett formulär rubrik.',
	'input_form_url' => 'Vänligen ange ett formulär länk.',
	'form_name_minimum' => 'Formulärets rubrik måste vara minst 2 tecken.',
	'form_url_minimum' => 'Formulärets länk måste vara minst 2 tecken.',
	'form_name_maximum' => 'Formulärets rubrik måste vara högst 32 tecken.',
	'form_url_maximum' => 'Formulärets länk måste vara högst 32 tecken.',
	'form_icon_maximum' => 'Formulärets ikon måste vara högst 64 tecken.',
	'input_field_name' => 'Vänligen ange ett fält namn.',
	'field_name_minimum' => 'Fält namnet måste vara minst 2 tecken.',
	'field_name_maximum' => 'Fält namnet måste vara högst 255 tecken.',
	
	// Submissions
	'submissions' => 'Inskickade',
	'submission_updated' => 'Submission updated successfully',
	'no_open_submissions' => 'Det finns inga öppna inskickningar just nu.',
	'no_closed_submissions' => 'Det finns inga stängda inskickningar just nu.',
	'form_x' => 'Formulär: {x}',
	'current_status_x' => 'Nuvarande Status: {x}',
	'last_updated' => 'Sist uppdaterad:',
	'your_submission_updated' => 'Your submission has been updated',
	'user' => 'User',
	'updated_by' => 'Updated by',
	'sort' => 'Sortieren',
	
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