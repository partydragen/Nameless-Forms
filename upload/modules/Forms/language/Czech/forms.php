<?php 
/*
 *	Made by Partydragen, translated by Fjuro
 *	https://github.com/partydragen/Nameless-Forms
 *	https://partydragen.com/
 *	NamelessMC version 2.0.0-pr12
 *
 *	License: MIT
 *
 *	Czech Language for Forms module
 */

$language = array(
	// Forms
	'forms' => 'Formuláře',
	'form' => 'Formulář',
	'new_form' => 'Nový formulář',
	'form_name' => 'Název formuláře',
	'form_url' => 'URL formuláře (s předchozím /, např. /priklad)',
	'form_icon' => 'Ikona formuláře',
	'link_location' => 'Umístění odkazu',
	'creating_new_form' => 'Vytváření nového formuláře',
	'form_created_successfully' => 'Formulář úspěšně vytvořen',
	'none_forms_defined' => 'Zatím jste nevytvořili žádné formuláře.',
	'delete_form' => 'Opravdu chcete odstranit tento formulář?</br>Varování: Všechna data související s tímto formulářem, jako otázky a odpovědi, budou smazána',
	'form_submitted' => 'Formulář úspěšně odeslán',
	'action' => 'Akce',
	'actions' => 'Akce',
	'guest' => 'Host',
	
	// Permissions
	'forms_view_submissions' => 'Panel &raquo; Formuláře &raquo; Odpovědi',
	'forms_manage' => 'Panel &raquo; Formuláře &raquo; Formuláře',
	'can_post_submission' => 'Může odesílat odpovědi',
	'can_view_own_submission' => 'Může zobrazit vlastní odpověď',
	'can_view_submissions' => 'Může zobrazovat odpovědi',
	'can_delete_submissions' => 'Může mazat odpovědi',
	'show_navigation_link_for_guest' => 'Zobrazit odkaz v navigaci hostům a zobrazit přihlašovací formulář, pokud nemají oprávnění na odpovědi',
	
	// Form
	'editing_x' => 'Úprava formuláře {x}', // Don't replace {x}
	'form_created_successfully' => 'Formulář úspěšně vytvořen.',
	'form_updated_successfully' => 'Formulář úspěšně upraven.',
	'form_deleted_successfully' => 'Formulář úspěšně odstraněn.',
	'enable_captcha' => 'Povolit CAPTCHA u tohoto formuláře?',
	
	// Fields
	'field' => 'Pole',
	'fields' => 'Pole',
	'new_field' => 'Nové pole',
	'field_name' => 'Název pole',
	'field_created_successfully' => 'Pole úspěšně vytvořeno',
	'field_updated_successfully' => 'Pole úspěšně upraveno',
	'field_deleted_successfully' => 'Pole úspěšně odstraněno',
	'new_field_for_x' => 'Vytváření nového pole ve formuláři {x}',
	'editing_field_for_x' => 'Úprava pole ve formuláři {x}',
	'none_fields_defined' => 'Zatím jste nevytvořili žádná pole.',
	'confirm_delete_field' => 'Opravdu chcete odstranit toto pole?',
	'options' => 'Možnosti',
	'options_help' => 'Každá možnost na nový řádek; může být ponecháno prázdné (pouze možnosti). Do tohoto boxu byste také měli zadat text nápovědy.',
	'field_order' => 'Pořadí pole',
	'delete_field' => 'Opravdu chcete odstranit toto pole?',
	'help_box' => 'Text nápovědy',
	'barrier' => 'Oddělovač',
	'number' => 'Číslo',
	'radio' => 'Výběr z možností',
	'checkbox' => 'Zaškrtávací políčka',
    'file' => 'File (Pictures)',
	'minimum_characters' => 'Minimální počet znaků (0 pro zakázání)',
	'maximum_characters' => 'Maximální počet znaků (0 pro zakázání)',

	// Statuses
	'statuses' => 'Stavy',
	'status' => 'Stav',
	'new_status' => 'Nový stav',
	'creating_status' => 'Vytváření nového stavu',
	'editing_status' => 'Úprava stavu',
	'marked_as_open' => 'Označeno jako otevřené',
	'status_name' => 'Název stavu',
	'status_html' => 'HTML stavu',
	'status_forms' => 'Formuláře stavu',
	'status_groups' => 'Skupiny stavu',
	'status_creation_success' => 'Status úspěšně vytvořen.',
	'status_creation_error' => 'Při vytváření stavu se vyskytla chyba. Ujistěte se, že HTML stavu není delší než 1024 znaků.',
	'status_edit_success' => 'Stav úspěšně upraven.',
	'status_deleted_successfully' => 'Stav úspěšně odstraněn.',
	'delete_status' => 'Opravdu chcete odstranit tento stav?',
	'select_statuses_to_form' => 'Vyberte stavy k použití u tohoto formuláře',
	'change_status_on_comment' => 'Změnit stav, když uživatel odešle komentář?',

	// Errors
	'input_form_name' => 'Zadejte prosím název formuláře.',
	'input_form_url' => 'Zadejte prosím URL formuláře.',
	'form_name_minimum' => 'Název formuláře musí obsahovat minimálně 2 znaky.',
	'form_url_minimum' => 'URL formuláře musí obsahovat minimálně 2 znaky.',
	'form_name_maximum' => 'Název formuláře může obsahovat maximálně 32 znaků.',
	'form_url_maximum' => 'URL formuláře může obsahovat maximálně 32 znaků.',
	'form_icon_maximum' => 'Ikona formuláře může obsahovat maximálně 64 znaků.',
	'input_field_name' => 'Zadejte prosím název pole.',
	'field_name_minimum' => 'Název pole musí obsahovat minimálně 2 znaky.',
	'field_name_maximum' => 'Název pole může obsahovat maximálně 255 znaků.',
	'x_field_minimum_y' => '{x} musí mít minimálně {y} znaků.',
	'x_field_maximum_y' => '{x} musí mít maximálně {y} znaků.',
    'comment_minimum' => 'The comment must be a minimum of 3 characters.',
    'comment_maximum' => 'The comment must be a maximum of 10000 characters.',
    'form_url_slash' => 'Form URL must begin with a /',
	
	// Submissions
	'submissions' => 'Odpovědi',
	'submission_updated' => 'Odpověď úspěšně upravena',
	'no_open_submissions' => 'Momentálně zde nejsou žádné otevřené odpovědi.',
	'no_closed_submissions' => 'Momentálně zde nejsou žádné uzavřené odpovědi.',
	'form_x' => 'Formulář: {x}',
	'current_status_x' => 'Stav: {x}',
	'last_updated' => 'Naposledy aktualizováno:',
	'your_submission_updated' => 'Vaše odpověď byla upravena',
	'user' => 'Uživatel',
	'updated_by' => 'Upravil',
	'sort' => 'Seřadit',
	'id_or_username' => 'ID nebo jméno',
	'confirm_delete_comment' => 'Opravdu chcete odstranit tento komentář?',
	'confirm_delete_submisssion' => 'Opravdu chcete odstranit tuto odpověď?',
	'delete_submissions_or_comments' => 'Mazat odpovědi nebo komentáře',
	'no_comment' => 'Žádný komentář',
	'anonymous' => 'Anonymní',
	'submit_as_anonymous' => 'Odesláno jako anonymní',
	'send_notify_email' => 'Poslat oznamovací e-mail (zpoždí odeslání)',
	
	// Update alerts
	'new_update_available_x' => 'Je dostupná nová aktualizace doplňku {x}',
	'new_urgent_update_available_x' => 'Je dostupná nová závažná aktualizace doplňku {x}. Aktualizujte jak nejdříve je to možné!',
	'current_version_x' => 'Současná verze doplňku: {x}',
	'new_version_x' => 'Nová verze doplňku: {x}',
	'view_resource' => 'Zobrazit stránku doplňku',
	
	// Hook
	'new_form_submission' => 'Nová odpověď ve formuláři',
	'updated_form_submission' => 'Nový komentář u odpovědi formuláře',
	'new_submission_text' => 'Nová odpověď vytvořena u formuláře {x} uživatelem {y}',
	'updated_submission_text' => 'Komentář nové odpovědi u formuláře {x} od uživatele {y}',
	
	// Email
	'submission_updated_subject' => 'Nová aktivita u odpovědi {x}',
	'submission_updated_message' => '
		U vaší odpovědi u formuláře {form} byla zjištěna nová aktivita.</br>
		</br>
		Aktuálnís stav: {status}</br>
		Aktualizoval: {updated_by}</br>
		Komentář: {comment}</br>
		</br>
		Celou vaši odpověď a změny si můžete zobrazit kliknutím na tento odkaz: <a href="{link}">{link}</a>
	'
);
