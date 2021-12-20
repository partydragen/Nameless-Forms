<?php 
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
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
    'can_post_submission' => 'Can post submission',
    'can_view_own_submission' => 'Can view own submission',
    'can_view_submissions' => 'Can view submissions',
    'can_delete_submissions' => 'Can delete submissions',
    'show_navigation_link_for_guest' => 'Show navigation link for guest and ask they to login if them don\'t have post permission',
    
    // Lomake
    'editing_x' => 'Muokataan {x}', // Don't replace {x}
    'form_created_successfully' => 'Lomake luotiin onnistuneesti.',
    'form_updated_successfully' => 'Lomake päivitettiin onnistuneesti.',
    'form_deleted_successfully' => 'Lomake poistettiin onnistuneesti.',
    'enable_captcha' => 'Enable Captcha on this form?',
    
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
    'number' => 'Number',
    'radio' => 'Radio',
    'checkbox' => 'Checkbox',
    'minimum_characters' => 'Minimum Characters (0 to disable)',
    'maximum_characters' => 'Maximum Characters (0 to disable)',
    
    // Statuses
    'statuses' => 'tilat',
    'status' => 'Tila',
    'new_status' => 'Uusi tila',
    'creating_status' => 'Luodaan uutta statusta',
    'editing_status' => 'Muokataan statusta',
    'marked_as_open' => 'Merkitty avoimeksi',
    'status_name' => 'Tilan nimi',
    'status_html' => 'Tilan HTML',
    'status_forms' => 'Select forms where this status will be displayed on. (Ctrl+click to select/deselect multiple)',
    'status_groups' => 'Select groups who are allowed to select this status. (Ctrl+click to select/deselect multiple)',
    'status_creation_success' => 'Tila luotiin onnistuneesti.',
    'status_creation_error' => 'Virhe luodessa tilaa. Varmista, että tilan HTML ei ole 1024 merkkiä pidempi.',
    'status_edit_success' => 'Tila muokattiin onnistuneesti.',
    'status_deleted_successfully' => 'Tila poistettiin onnistuneesti.',
    'delete_status' => 'Oletko varma, että haluat poistaa tämän tilan?',
    'select_statuses_to_form' => 'Select statuses to be used on this form',
    'change_status_on_comment' => 'Change status when user is commenting?',

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
    'x_field_minimum_y' => '{x} must be a minimum of {y} characters.',
    'x_field_maximum_y' => '{x} must be a maximum of {y} characters.',
    
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
    'id_or_username' => 'ID or Username',
    'confirm_delete_comment' => 'Are you sure you want to delete this comment?',
    'confirm_delete_submisssion' => 'Are you sure you want to delete this submission?',
    'delete_submissions_or_comments' => 'Delete submissions or comments',
    'no_comment' => 'No comment',
    'anonymous' => 'Anonymous',
    'submit_as_anonymous' => 'Submit as anonymous',
    'send_notify_email' => 'Send notify email (Will add submit slowness)',
    
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
    'updated_submission_text' => 'New submission comment in {x} by {y}',
    
    // Email
    'submission_updated_subject' => 'Your {x} submission has been updated',
    'submission_updated_message' => '
        There has been an update regarding your submission for {form}.</br>
        </br>
        Current Status: {status}</br>
        Updated by: {updated_by}</br>
        Comment: {comment}</br>
        </br>
        You can view your full submission and updates by clicking here <a href="{link}">{link}</a>
    '
);