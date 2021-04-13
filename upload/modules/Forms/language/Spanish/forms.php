<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  Spanish Language for Forms module
 */

$language = array(
	// Forms
	'forms' => 'Formularios',
	'form' => 'Formulario',
	'new_form' => 'Nuevo formulario',
	'form_name' => 'Nombre del formulario',
	'form_url' => 'URL del formulario (Use el / en las url, asi /ejemplo)',
	'form_icon' => 'Icono del formulario',
	'link_location' => 'Enlace del formulario',
	'creating_new_form' => 'Creando un formulario',
	'form_created_successfully' => 'Formulario creado correctamente',
	'none_forms_defined' => 'No hay formularios todavía.',
	'delete_form' => '¿Está seguro de que desea eliminar este formulario? </br> Advertencia: Todos los datos que pertenecen a este formulario se eliminarán como preguntas y envíos.',
	'form_submitted' => 'Formulario enviado correctamente',
	'action' => 'Accion',
	'actions' => 'Acciones',
	'guest' => 'Invitado',
	
	// Permissions
	'forms_view_submissions' => 'StaffCP &raquo; Formularios &raquo; Envios',
	'forms_manage' => 'StaffCP &raquo; Formularios',
    'can_post_submission' => 'Can post submission',
    'can_view_own_submission' => 'Can view own submission',
    'can_view_submissions' => 'Can view submissions',
    'can_delete_submissions' => 'Can delete submissions',
    'show_navigation_link_for_guest' => 'Show navigation link for guest and ask they to login if them don\'t have post permission',
	
	// Form
	'editing_x' => 'Editando {x}', // Don't replace {x}
	'form_created_successfully' => 'Formulario creado correctamente.',
	'form_updated_successfully' => 'Formulario actualizado correctamente.',
	'form_deleted_successfully' => 'Formulario eliminado correctamente.',
    'enable_captcha' => 'Enable Captcha on this form?',
	
	// Fields
	'field' => 'Campo',
	'fields' => 'Campos',
	'new_field' => 'Nuevo Campo',
	'field_name' => 'Nombre del Campo',
	'field_created_successfully' => 'Campo creado correctamente',
	'field_updated_successfully' => 'Campo actualizado correctamete',
	'field_deleted_successfully' => 'Campo eliminado correctamente',
	'new_field_for_x' => 'Creando un nuevo campo para {x}',
	'editing_field_for_x' => 'Campo de edición para {x}',
	'none_fields_defined' => 'No hay campos aún.',
	'confirm_delete_field' => '¿Seguro que quieres eliminar este campo?',
	'options' => 'Opcciones',
	'options_help' => 'Cada opción en una nueva línea; puede dejarse vacío (solo opciones).',
	'field_order' => 'Orden de los campos',
	'delete_field' => '¿Seguro que quieres eliminar este campo?',
	'help_box' => 'Help Text',
	'barrier' => 'Dividing Line',
    'number' => 'Number',
	
	// Statuses
	'statuses' => 'Estados',
	'status' => 'Estado',
	'new_status' => 'Nuevo Estado',
	'creating_status' => 'Creando estado',
	'editing_status' => 'Editando estado',
	'marked_as_open' => 'Marcar como abierto',
	'status_name' => 'Nombre del estado',
	'status_html' => 'Estado en HTML',
	'status_forms' => 'Select forms where this status will be displayed on. (Ctrl+click to select/deselect multiple)',
	'status_groups' => 'Select groups who are allowed to select this status. (Ctrl+click to select/deselect multiple)',
	'status_creation_success' => 'Estado creado correctamente.',
	'status_creation_error' => 'Error al crear un estado. Asegúrese de que el estado html no tenga más de 1024 caracteres.',
	'status_edit_success' => 'Estado actualizado con exito.',
	'status_deleted_successfully' => 'Estado eliminado con exito.',
	'delete_status' => 'Seguro que quieres eliminar este estado?',

	// Errors
	'input_form_name' => 'Por favor introdusca un nombre a el formulario.',
	'input_form_url' => 'Por favor introdusca una url valida para el formulario',
	'form_name_minimum' => 'Introdusca un nombre de mas de 2 letras por favor.',
	'form_url_minimum' => 'Introdusca una url de mas de 2 letras por favor.',
	'form_name_maximum' => 'El nombre que ha ingresado tiene mas de 32 letras.',
	'form_url_maximum' => 'La url que ah ingresado tiene mas de 32 letras',
	'form_icon_maximum' => 'El icono no puede tener mas de 64 letras.',
	'input_field_name' => 'Por favor introdusca un nombre al campo',
	'field_name_minimum' => 'El nombre del campo debe tener un mínimo de 2 caracteres.',
	'field_name_maximum' => 'El nombre del campo debe tener un máximo de 255 caracteres',
	
	// Submissions
	'submissions' => 'Envios',
	'submission_updated' => 'Envío actualizado con éxito',
	'no_open_submissions' => 'Actualmente no hay envíos abiertos',
	'no_closed_submissions' => 'Actualmente no hay envíos cerrados.',
	'form_x' => 'Formulario: {x}',
	'current_status_x' => 'Estado actual: {x}',
	'last_updated' => 'Ultima actualizacion:',
	'your_submission_updated' => 'Tu envio fue actualizado',
	'user' => 'Usuario',
	'updated_by' => 'Actualizado por',
	'sort' => 'Sort',
    'id_or_username' => 'ID or Username',
    'confirm_delete_comment' => 'Are you sure you want to delete this comment?',
    'confirm_delete_submisssion' => 'Are you sure you want to delete this submission?',
    'delete_submissions_or_comments' => 'Delete submissions or comments',
	
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