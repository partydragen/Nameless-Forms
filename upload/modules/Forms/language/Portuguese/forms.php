<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 *
 *  Portuguese Language for Forms module
 */

$language = array(
	// Forms
	'forms' => 'Formulários',
	'form' => 'Formulário',
	'new_form' => 'Novo Formulário',
	'form_name' => 'Nome do Formulário',
	'form_url' => 'URL do Formulário (com a / na frente, ex.: /exemplo)',
	'form_icon' => 'Ícone do Formulário',
	'link_location' => 'Local do Link',
	'creating_new_form' => 'Criando Novo Formulário',
	'form_created_successfully' => 'Formulário criado com sucesso',
	'none_forms_defined' => 'Não há nenhum formulário ainda.',
	'delete_form' => 'Tem certeza que quer excluir esse formulário?</br>Aviso: Todos os dados que pertencem a esse formulário serão deletados, como perguntas e envios',
	'form_submitted' => 'Formulário enviado com sucesso',
	'action' => 'Ação',
	'actions' => 'Ações',
	'guest' => 'Visitante',
	
	// Permissions
	'forms_view_submissions' => 'StaffCP &raquo; Formulários &raquo; Envios',
	'forms_manage' => 'StaffCP &raquo; Formulários &raquo; Formulários',
    'can_post_submission' => 'Can post submission',
    'can_view_own_submission' => 'Can view own submission',
    'can_view_submissions' => 'Can view submissions',
    'can_delete_submissions' => 'Can delete submissions',
    'show_navigation_link_for_guest' => 'Show navigation link for guest and ask they to login if them don\'t have post permission',
	
	// Form
	'editing_x' => 'Editando {x}', // Don't replace {x}
	'form_created_successfully' => 'Formulário criado com sucesso.',
	'form_updated_successfully' => 'Formulário atualizado com sucesso.',
	'form_deleted_successfully' => 'Formulário excluído com sucesso.',
    'enable_captcha' => 'Habilitar o Captcha nesse formulário?',
	
	// Fields
	'field' => 'Campo',
	'fields' => 'Campos',
	'new_field' => 'Novo Campo',
	'field_name' => 'Nome do Campo',
	'field_created_successfully' => 'Campo criado com sucesso',
	'field_updated_successfully' => 'Campo atualizado com sucesso',
	'field_deleted_successfully' => 'Campo excluído com sucesso',
	'new_field_for_x' => 'Criando novo campo para {x}',
	'editing_field_for_x' => 'Editando campo para {x}',
	'none_fields_defined' => 'Não há nenhum campo ainda.',
	'confirm_delete_field' => 'Tem certeza que quer excluir esse campo?',
	'options' => 'Opções',
	'options_help' => 'Cada opção em uma nova linha; a linha pode ser deixada em branco (só pra opções). O texto de ajuda também deve ser colocado neste campo',
	'field_order' => 'Ordem do Campo',
	'delete_field' => 'Tem certeza que quer excluir esse campo?',
    'help_box' => 'Texto de ajuda',
    'barrier' => 'Linha divisória',
    'number' => 'Número',
	
	// Statuses
	'statuses' => 'Estados',
	'status' => 'Estado',
	'new_status' => 'Novo Estado',
	'creating_status' => 'Criando novo estado',
	'editing_status' => 'Editando estado',
	'marked_as_open' => 'Significa estado aberto',
	'status_name' => 'Nome do Estado',
	'status_html' => 'HTML do Estado',
	'status_forms' => 'Selecione os formulários nos quais esse estado será exibido. (Ctrl+clique para selecionar/desmarcar vários)',
	'status_groups' => 'Selecione os grupos que têm permissão para selecionar este estado. (Ctrl+clique para selecionar/desmarcar vários)',
	'status_creation_success' => 'Estado criado com sucesso.',
	'status_creation_error' => 'Erro ao criar um estado. Por favor certifique-se que o HTML do estado não é maior que 1.024 caracteres.',
	'status_edit_success' => 'Estado editado com sucesso.',
	'status_deleted_successfully' => 'Estado excluído com sucesso.',
	'delete_status' => 'Tem certeza que quer excluir esse estado?',

	// Errors
	'input_form_name' => 'Por favor insira o nome do formulário.',
	'input_form_url' => 'Por favor insira o URL do formulário.',
	'form_name_minimum' => 'O nome do formulário deve ser de no mínimo 2 caracteres.',
	'form_url_minimum' => 'O URL do formulário deve ser de no mínimo 2 caracteres.',
	'form_name_maximum' => 'O nome do formulário deve ser de no máximo 32 caracteres.',
	'form_url_maximum' => 'O URL do formulário deve ser de no máximo 32 caracteres.',
	'form_icon_maximum' => 'O ícone do formulário deve ser de no máximo 64 caracteres.',
	'input_field_name' => 'Por favor insira o nome do campo.',
	'field_name_minimum' => 'O nome do campo deve ser de no mínimo 2 caracteres.',
	'field_name_maximum' => 'O nome do campo deve ser de no máximo 255 caracteres.',
	
	// Submissions
	'submissions' => 'Envios',
	'submission_updated' => 'Envios atualizados com sucesso',
	'no_open_submissions' => 'Não há nenhum envio aberto ainda.',
	'no_closed_submissions' => 'Não há nenhum envio fechado ainda.',
	'form_x' => 'Formulário: {x}',
	'current_status_x' => 'Estado atual: {x}',
	'last_updated' => 'Última modificação:',
	'your_submission_updated' => 'Seu envio foi atualizado',
	'user' => 'Usuário',
	'updated_by' => 'Modificado por',
	'sort' => 'Ordenar',
    'confirm_delete_comment' => 'Tem certeza que deseja deletar este comentário?',
    'confirm_delete_submisssion' => 'Tem certeza de que deseja excluir este envio?',
    'delete_submissions_or_comments' => 'Excluir envios ou comentários',
	
	// Update alerts
	'new_update_available_x' => 'Há uma nova atualização disponível para o módulo {x}',
	'new_urgent_update_available_x' => 'Há uma nova atualização urgente disponível para o módulo {x}. Por favor, atualize o mais rápido possível!',
	'current_version_x' => 'Versão atual do módulo: {x}',
	'new_version_x' => 'Nova versão do módulo: {x}',
	'view_resource' => 'Ver Recurso',
    
    // Hook
    'new_form_submission' => 'Envio de novo formulário',
    'updated_form_submission' => 'Novo comentário de envio do formulário',
    'new_submission_text' => 'Novo envio criado em {x} por {y}',
    'updated_submission_text' => 'Novo comentário de envio em {x} por {y}'
    //'updated_submission_text' => 'Submission updated in {x} by {y}'
);
