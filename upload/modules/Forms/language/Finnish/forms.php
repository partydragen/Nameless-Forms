<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr8
 *
 *  License: MIT
 *
 *  Finnish Language for Forms module
 */

$language = array(
	// Lomakes
	'forms' => 'Lomakkeet',
	'form' => 'Lomake',
	'new_form' => 'Uusi lomake',
	'form_name' => 'Lomakkeen nimi',
	'form_url' => 'Lomakkeen URL (ennakoivalla /, esim. /esimerkki)',
	'form_icon' => 'Lomakkeen kuvake',
	'link_location' => 'Linkin sijainti',
	'creating_new_form' => 'Luodaan uutta lomaketta',
	'form_created_successfully' => 'Lomake luotiin onnistuneesti',
	'none_forms_defined' => 'Lomakkeita ei ole vielä luotu.',
	'delete_form' => 'Oletko varma, että haluat poistaa tämän lomakkeen?</br>Varoitus: Kaikki tiedot poistetaan, kuten hakemukset ja kysymykset',
	'form_submitted' => 'Hakemus lähetetty onnistuneesti',
	'action' => 'Toiminto',
	'actions' => 'Toiminnot',
	'guest' => 'Vieras',
	
	// Permissions
	'forms_view_submissions' => 'AdminCP &raquo; Lomakkeet &raquo; Hakemukset',
	'forms_manage' => 'AdminCP &raquo; Lomakkeet &raquo; Lomakkeet',
	
	// Lomake
	'editing_x' => 'Muokataan {x}', // Don't replace {x}
	'form_created_successfully' => 'Lomake luotiin onnistuneesti.',
	'form_updated_successfully' => 'Lomake päivitettiin onnistuneesti.',
	'form_deleted_successfully' => 'Lomake poistettiin onnistuneesti.',
	'allow_guests' => 'Voiko vieraat tarkastella lomaketta?',
	'allow_guests_help' => 'Vieraat voivat lähettää hakemuksia kirjautumatta sisään. Ota huomioon, että he eivät voi tarkastella hakemusta jälkeenpäin.',
	'can_user_view' => 'Voiko käyttäjä tarkastella omaa hakemusta?',
	'can_user_view_help' => 'Käyttäjä voi tarkastella hakemusta ja nähdä kommenttiosion. Käyttäjä saa myös ilmoituksen, kun tila vaihtuu tai joku kommentoi hakemusta. Huomaa, että tämä ei toimi vieraille.',
	
	// Fields
	'field' => 'Kenttä',
	'fields' => 'Kentät',
	'new_field' => 'Uusi kenttä',
	'field_name' => 'Kentän nimi',
	'field_created_successfully' => 'Kenttä luotiin onnistuneesti',
	'field_updated_successfully' => 'Kenttä päivitettiin onnistuneesti',
	'field_deleted_successfully' => 'Kenttä poistettiin onnistuneesti',
	'new_field_for_x' => 'Luodaan uutta kenttää lomakkeelle {x}',
	'editing_field_for_x' => 'Muokataan kenttää lomakkeelle {x}',
	'none_fields_defined' => 'Kenttiä ei vielä ole.',
	'confirm_delete_field' => 'Oletko varma, että haluat poistaa tämän kentän?',
	'options' => 'Valinnat',
	'options_help' => 'Jokainen valinta uudelle riville; voi jättää tyhjäksi (vain valinnat). Ohjekirja olisi myös lisättävä tähän kenttään',
	'field_order' => 'Kenttien järjestys',
	'delete_field' => 'Oletko varma, että haluat poistaa tämän kentän?',
    'help_box' => 'Ohjeteksti',
    'barrier' => 'Eroittava viiva',
	
	// Statuses
	'statuses' => 'tilat',
	'status' => 'Tila',
	'new_status' => 'Uusi tila',
	'creating_status' => 'Luodaan uutta statusta',
	'editing_status' => 'Muokataan statusta',
	'marked_as_open' => 'Merkitty avoimeksi',
	'status_name' => 'Tilan nimi',
	'status_html' => 'Tilan HTML',
	'status_forms' => 'Tilan lomakkeet',
	'status_groups' => 'Tilan ryhmät',
	'status_creation_success' => 'Tila luotiin onnistuneesti.',
	'status_creation_error' => 'Virhe luodessa tilaa. Varmista, että tilan HTML ei ole 1024 merkkiä pidempi.',
	'status_edit_success' => 'Tila muokattiin onnistuneesti.',
	'status_deleted_successfully' => 'Tila poistettiin onnistuneesti.',
	'delete_status' => 'Oletko varma, että haluat poistaa tämän tilan?',

	// Errors
	'input_form_name' => 'Anna lomakkeen nimi.',
	'input_form_url' => 'Anna lomakkeen URL.',
	'form_name_minimum' => 'Lomakkeen nimi täytyy olla vähintään 2 characters.',
	'form_url_minimum' => 'Lomakkeen URL täytyy olla vähintään 2 characters.',
	'form_name_maximum' => 'Lomakkeen nimi saa olla enintään 32 characters.',
	'form_url_maximum' => 'Lomakkeen URL saa olla enintään 32 characters.',
	'form_icon_maximum' => 'Lomakkeen kuvake saa olla enintään 64 characters.',
	'input_field_name' => 'Anna kentän nimi.',
	'field_name_minimum' => 'Kentän nimi täytyy olla vähintään 2 characters.',
	'field_name_maximum' => 'Kentän nimi saa olla enintään 255 characters.',
	
	// Submissions
	'submissions' => 'Hakemukset',
	'submission_updated' => 'Hakemus päivitettiin onnistuneesti',
	'no_open_submissions' => 'Ei avoimia hakemuksia.',
	'no_closed_submissions' => 'Ei suljettuja hakemuksia.',
	'form_x' => 'Lomake: {x}',
	'current_status_x' => 'Nykyinen tila: {x}',
	'last_updated' => 'Päivitetty viimeksi:',
	'your_submission_updated' => 'Hakemuksesi on päivitetty',
	'user' => 'Käyttäjä',
	'updated_by' => 'Päivittänyt',
	'sort' => 'Sort',
	
	// Update alerts
	'new_update_available_x' => 'There is a new update available for the module {x}',
	'new_urgent_update_available_x' => 'There is a new urgent update available for the module {x}. Please update as soon as possible!',
	'current_version_x' => 'Current module version: {x}',
	'new_version_x' => 'New module version: {x}',
	'view_resource' => 'View Resource',
);