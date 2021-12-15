<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr11
 *
 *  License: MIT
 *
 *  Dutch language for the forms module
 */

$language = array(
	// Forms
	'forms' => 'Formulieren',
	'form' => 'Formulier',
	'new_form' => 'New Formulier',
	'form_name' => 'Formulier naam',
	'form_url' => 'Formulier URL (met voorgaand /, bijv /voorbeeld)',
	'form_icon' => 'Formulier Icoon',
	'link_location' => 'Link Locatie',
	'creating_new_form' => 'Nieuw formulier aan het maken',
	'form_created_successfully' => 'Formulier successvol aangemaakt',
	'none_forms_defined' => 'Er zijn nog geen formulieren.',
	'delete_form' => 'Ben je zeker dat je dit formulier wilt verwijderen?</br>Waarschuwing: Alle data die tot dit formulier behoort zal verwijderd worden zoals vragen en inzendingen',
	'form_submitted' => 'Formulier successvol ingediend',
	'action' => 'Actie',
	'actions' => 'Acties',
	'guest' => 'Gast',
	
	// Permissions
	'forms_view_submissions' => 'StaffCP &raquo; Formulieren &raquo; Inzendingen',
	'forms_manage' => 'StaffCP &raquo; Formulieren &raquo; Formulieren',
  'can_post_submission' => 'Kan inzendingen maken',
  'can_view_own_submission' => 'Kan eigen inzendingen bekijken',
  'can_view_submissions' => 'Kan inzendingen bekijken',
  'can_delete_submissions' => 'Kan inzendingen verwijderen',
  'show_navigation_link_for_guest' => 'Toon navigatie link voor gasten en vraag om in te loggen als ze geen toegang hebben',
	
	// Form
	'editing_x' => '{x} aan het wijzigen', // Don't replace {x}
	'form_created_successfully' => 'Formulier successvol aangemaakt.',
	'form_updated_successfully' => 'Formulier successvol aangepast.',
	'form_deleted_successfully' => 'Form successvol verwijderd.',
  'enable_captcha' => 'Zet captcha aan voor dit formulier?',
	
	// Fields
	'field' => 'Veld',
	'fields' => 'Velden',
	'new_field' => 'Nieuw veld',
	'field_name' => 'Veldnaam',
	'field_created_successfully' => 'Veld successvol aangemaakt',
	'field_updated_successfully' => 'Veld successvol aangepast',
	'field_deleted_successfully' => 'Veld successvol verwijderd',
	'new_field_for_x' => 'Nieuw veld aan het aanmaken voor {x}',
	'editing_field_for_x' => 'Veld voor {x} aan het aanpassen',
	'none_fields_defined' => 'Er zijn nog geen velden.',
	'confirm_delete_field' => 'Ben je zeker dat je dit veld wilt verwijderen?',
	'options' => 'Opties',
	'options_help' => 'Elke optie op een nieuwe lijn; kan leeggelaten worden (alleen voor opties). Help text moet ook in dit vak staan.',
	'field_order' => 'Veld volgorde',
	'delete_field' => 'Ben je zeker dat je dit veld wilt verwijderen?',
	'help_box' => 'Help Text',
	'barrier' => 'Verdelende Line',
  'number' => 'Nummer',
  'radio' => 'Radio',
  'checkbox' => 'Selectievakje',
  'minimum_characters' => 'Minimum Karakters (0 om uit te schakelen)',
  'maximum_characters' => 'Maximum Karakters (0 om uit te schakelen)',

	// Statuses
	'statuses' => 'Statussen',
	'status' => 'Status',
	'new_status' => 'Nieuwe Status',
	'creating_status' => 'Nieuwe status aan het maken',
	'editing_status' => 'Status aan het bewerken',
	'marked_as_open' => 'Gemarkeerd als open',
	'status_name' => 'Status Naam',
	'status_html' => 'Status HTML',
	'status_forms' => 'Selecteer formulieren waar deze status getoont zal worden. (Ctrl+click om meerdere te selecteren/deselecteren)',
	'status_groups' => 'Selecteer groepen die zijn toegestaan om deze status te selecteren. (Ctrl+click om meerdere te selecteren/deselecteren)',
	'status_creation_success' => 'Status successvol aangemaakt.',
	'status_creation_error' => 'Error tijdens het aanmaken van een nieuwe status. Zorg ervoor dat de status html niet langer is dan 1024 karakters.',
	'status_edit_success' => 'Status successvol aangepast.',
	'status_deleted_successfully' => 'Status successvol verwijderd.',
	'delete_status' => 'Ben je zeker dat je deze status wilt verwijderen?',
  'select_statuses_to_form' => 'Selecteer statussen die gebruikt zullen worden op dit formulier',
  'change_status_on_comment' => 'Wijzig status wanneer de gebruiker reageert?',
    
	// Errors
	'input_form_name' => 'Vul alstublieft een formuliernaam in.',
	'input_form_url' => 'Vul alstublieft een formulier URL in.',
	'form_name_minimum' => 'De naam van het formulier moet minimaal 2 karakters lang zijn.',
	'form_url_minimum' => 'De URL van het formulier moet minimaal 2 karakters lang zijn.',
	'form_name_maximum' => 'De naam van het formulier mag maximaal 32 karakters lang zijn.',
	'form_url_maximum' => 'De URL van het formulier mag maximaal 32 karakters lang zijn.',
	'form_icon_maximum' => 'Het formulier icoon mag maximaal 64 karakters lang zijn.',
	'input_field_name' => 'Vul alstublieft een veldnaam in.',
	'field_name_minimum' => 'Het veldnaam moet minimaal 2 karakters lang zijn.',
	'field_name_maximum' => 'THet veldnaam mag maximaal 255 karakters lang zijn.',
  'x_field_minimum_y' => '{x} moet minimaal {y} karakters lang zijn.',
  'x_field_maximum_y' => '{x} max maximaal {y} karakters lang zijn.',
	
	// Submissions
	'submissions' => 'Inzendingen',
	'submission_updated' => 'Inzending successvol aangepast',
	'no_open_submissions' => 'Er zijn op dit moment geen open inzendingen.',
	'no_closed_submissions' => 'Er zijn op dit moment geen gesloten inzendingen.',
	'form_x' => 'Formulier: {x}',
	'current_status_x' => 'Huidige status: {x}',
	'last_updated' => 'Laatst bijgewerkt:',
	'your_submission_updated' => 'Je inzending is bijgewerkt',
	'user' => 'Gebruiker',
	'updated_by' => 'Bijgewerkt door',
	'sort' => 'Sorteer',
  'id_or_username' => 'ID of Gebruikersnaam',
  'confirm_delete_comment' => 'Ben je zeker dat je deze opmerking wilt verwijderen?',
  'confirm_delete_submisssion' => 'Ben je zeker dat je deze inzending wilt verwijderen?',
  'delete_submissions_or_comments' => 'Verwijder inzending of opmerking',
  'no_comment' => 'Geen opmerkingen',
  'anonymous' => 'Anoniem',
  'submit_as_anonymous' => 'Zend anoniem in',
  'send_notify_email' => 'Zend notificatie email (voegt inzend vertraging toe)',
	
	// Update alerts
	'new_update_available_x' => 'Er is een nieuwe update voor de module {x}',
	'new_urgent_update_available_x' => 'Er is een nieuwe dringende update beschikbaar voor de module {x}. Update zo snel mogelijk alstublieft!',
	'current_version_x' => 'Huidige module versie: {x}',
	'new_version_x' => 'Nieuwe module versie: {x}',
	'view_resource' => 'Bekijk bron',
    
  // Hook
  'new_form_submission' => 'Nieuw formulier ingediend',
  'updated_form_submission' => 'Nieuwe opmerking bij een formulier',
  'new_submission_text' => 'Nieuwe inzending aangemaakt in {x} door {y}',
  'updated_submission_text' => 'Nieuwe opmerking voor een inzending in {x} door {y}',
    
  // Email
  'submission_updated_subject' => 'Je {x} inzending is geupdate',
  'submission_updated_message' => '
      Er is een update over je inzending voor {form}.</br>
      </br>
      Huidige Status: {status}</br>
      Aangepast door: {updated_by}</br>
      Opmerking: {comment}</br>
      </br>
      Je kan je volledige inzending bekijken door hier te klikken <a href="{link}">{link}</a>
  '
);
