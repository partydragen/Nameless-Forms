<?php
/*
 *  Made by RobiNN
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Slovak Language for Forms module
 */

$language = array(
    // Forms
    'forms' => 'Formuláre',
    'form' => 'Formulár',
    'new_form' => 'Nový formulár',
    'form_name' => 'Názov formulára',
    'form_url' => 'Cesta formulára (s predchádzajúcim /, napr. /priklad)',
    'form_icon' => 'Ikona formulára',
    'link_location' => 'Umiestnenie odkazu',
    'creating_new_form' => 'Vytváranie nového formulára',
    'form_created_successfully' => 'Formulár bol úspešne vytvorený',
    'none_forms_defined' => 'Zatiaľ neexistujú žiadne formuláre.',
    'delete_form' => 'Naozaj chcete odstrániť tento formulár?</br>Upozornenie: Všetky údaje, ktoré patria do tohto formulára, budú odstránené, ako sú otázky a odpovede',
    'form_submitted' => 'Formulár bol úspešne odoslaný',
    'action' => 'Akcia',
    'actions' => 'Akcie',
    'guest' => 'Hosť',

    // Permissions
    'forms_view_submissions' => 'StaffCP &raquo; Formuláre &raquo; Odpovede',
    'forms_manage' => 'StaffCP &raquo; Formuláre &raquo; Formuláre',
    'can_post_submission' => 'Môže odosielať odpoveď',
    'can_view_own_submission' => 'Môže zobraziť vlastnú odpoveď',
    'can_view_submissions' => 'Môže zobraziť odpovede',
    'can_delete_submissions' => 'Môže mazať odpovede',
    'show_navigation_link_for_guest' => 'Zobraziť odkaz v navigácii hosťom a požiadať ich, aby sa prihlásili, ak nemajú povolenie na odpovede',

    // Form
    'editing_x' => 'Úprava formulára {x}', // Don't replace {x}
    'form_created_successfully' => 'Formulár bol úspešne vytvorený.',
    'form_updated_successfully' => 'Formulár bol úspešne aktualizovaný.',
    'form_deleted_successfully' => 'Formulár bol úspešne odstránený.',
    'enable_captcha' => 'Povoliť Captcha v tomto formulári?',

    // Fields
    'field' => 'Pole',
    'fields' => 'Polia',
    'new_field' => 'Nové pole',
    'field_name' => 'Názov poľa',
    'field_created_successfully' => 'Pole bolo úspešne vytvorené',
    'field_updated_successfully' => 'Pole bolo úspešne aktualizované',
    'field_deleted_successfully' => 'Pole bolo úspešne odstránené',
    'new_field_for_x' => 'Vytváranie nového poľa pre {x}',
    'editing_field_for_x' => 'Úprava poľa pre {x}',
    'none_fields_defined' => 'Zatiaľ tu nie sú žiadne polia.',
    'confirm_delete_field' => 'Naozaj chcete odstrániť toto pole?',
    'options' => 'Možnosti',
    'options_help' => 'Každá možnosť na nový riadok; môže zostať prázdne (len možnosti). Do tohto poľa by ste mali vložiť aj pomocný text.',
    'field_order' => 'Poradie poľa',
    'delete_field' => 'Naozaj chcete odstrániť toto pole?',
    'help_box' => 'Pomocný text',
    'barrier' => 'Oddeľovač',
    'number' => 'Číslo',
    'radio' => 'Výber z možností',
    'checkbox' => 'Zaškrtávacie políčka',
    'file' => 'File (Pictures)',
    'minimum_characters' => 'Minimálny počet znakov (0 na vypnutie)',
    'maximum_characters' => 'Maximálny počet znakov (0 na vypnutie)',

    // Statuses
    'statuses' => 'Stavy',
    'status' => 'Stav',
    'new_status' => 'Nový stav',
    'creating_status' => 'Vytváranie nového stavu',
    'editing_status' => 'Úprava stavu',
    'marked_as_open' => 'Označené ako otvorené',
    'status_name' => 'Názov stavu',
    'status_html' => 'HTML stavu',
    'status_forms' => 'Vyberte formuláre, na ktorých sa bude tento stav zobrazovať. (Ctrl+kliknutie pre výber/zrušenie výberu viacerých)',
    'status_groups' => 'Vyberte skupiny, ktoré môžu vybrať tento stav. (Ctrl+kliknutie pre výber/zrušenie výberu viacerých)',
    'status_creation_success' => 'Stav bol úspešne vytvorený.',
    'status_creation_error' => 'Chyba pri vytváraní stavu. Uistite sa, že html stavu nie je dlhšie ako 1024 znakov.',
    'status_edit_success' => 'Stav bol úspešne upravený.',
    'status_deleted_successfully' => 'Stav bol úspešne odstránený.',
    'delete_status' => 'Naozaj chcete odstrániť tento stav?',
    'select_statuses_to_form' => 'Vyberte stavy, ktoré sa majú použiť v tomto formulári',
    'change_status_on_comment' => 'Zmeniť stav, keď používateľ odošle komentár?',

    // Errors
    'input_form_name' => 'Prosím zadajte názov formulára.',
    'input_form_url' => 'Prosím zadajte url formulára.',
    'form_name_minimum' => 'Názov formulára musí mať minimálne 2 znaky.',
    'form_url_minimum' => 'URL formulára musí mať minimálne 2 znaky.',
    'form_name_maximum' => 'Názov formulára môže mať maximálne 32 znakov.',
    'form_url_maximum' => 'URL formulára môže mať maximálne 32 znakov.',
    'form_icon_maximum' => 'Ikona formulára môže mať maximálne 64 znakov.',
    'input_field_name' => 'Prosím zadajte názov poľa.',
    'field_name_minimum' => 'Názov poľa musí mať minimálne 2 znaky.',
    'field_name_maximum' => 'Názov poľa môže mať maximálne 255 znakov.',
    'x_field_minimum_y' => '{x} musí mať minimálne {y} znakov.',
    'x_field_maximum_y' => '{x} môže mať maximálne {y} znakov.',
    'comment_minimum' => 'The comment must be a minimum of 3 characters.',
    'comment_maximum' => 'The comment must be a maximum of 10000 characters.',
    'form_url_slash' => 'Form URL must begin with a /',

    // Submissions
    'submissions' => 'Odpovede',
    'submission_updated' => 'Odpoveď bola úspešne aktualizovaná',
    'no_open_submissions' => 'Momentálne nie sú žiadne otvorené odpovede.',
    'no_closed_submissions' => 'Momentálne nie sú žiadne zatvorené odpovede.',
    'form_x' => 'Formulár: {x}',
    'current_status_x' => 'Aktuálny stav: {x}',
    'last_updated' => 'Naposledy aktualizované:',
    'your_submission_updated' => 'Vaša odpoveď bola aktualizovaná',
    'user' => 'Užívateľ',
    'updated_by' => 'Aktualizoval/a',
    'sort' => 'Zoradiť',
    'id_or_username' => 'ID alebo používateľské meno',
    'confirm_delete_comment' => 'Naozaj chcete odstrániť tento komentár?',
    'confirm_delete_submisssion' => 'Naozaj chcete odstrániť túto odpoveď?',
    'delete_submissions_or_comments' => 'Odstrániť odpovede alebo komentáre',
    'no_comment' => 'Žiadny komentár',
    'anonymous' => 'Anonymný',
    'submit_as_anonymous' => 'Odoslať ako anonymný',
    'send_notify_email' => 'Odoslať email s upozornením (oneskorí odoslanie)',
    'updated_submission_status' => 'Status changed from {status} to {new_status}',

    // Update alerts
    'new_update_available_x' => 'K dispozícii je nová aktualizácia pre modul {x}',
    'new_urgent_update_available_x' => 'Pre modul {x} je k dispozícii nová urgentná aktualizácia. Aktualizujte prosím čo najskôr!',
    'current_version_x' => 'Aktuálna verzia modulu: {x}',
    'new_version_x' => 'Nová verzia modulu: {x}',
    'view_resource' => 'Zobraziť stránku modulu',

    // Hook
    'new_form_submission' => 'Nová odpoveď vo formulári',
    'updated_form_submission' => 'Nový komentár v odpovedi formulára',
    'updated_form_submission_staff' => 'New form submission comment from staff',
    'new_submission_text' => 'Nový príspevok vytvorený v {x} používateľom {y}',
    'updated_submission_text' => 'Nový komentár v odpovedi vytvorený v {x} používateľom {y}',

    // Email
    'submission_updated_subject' => 'Váš príspevok {x} bol aktualizovaný',
    'submission_updated_message' => '
        Došlo k aktualizácii vašej odpovede v {form}.</br>
        </br>
        Aktuálny stav: {status}</br>
        Aktualizoval/a: {updated_by}</br>
        Komentár: {comment}</br>
        </br>
        Kliknutím sem si môžete pozrieť celu vašu odpoveď a aktualizácie <a href="{link}">{link}</a>
    '
);
