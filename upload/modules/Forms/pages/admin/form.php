<?php 
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Forms module - admin form page
 */

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	} else {
		// Check the user has re-authenticated
		if(!$user->isAdmLoggedIn()){
			// They haven't, do so now
			Redirect::to(URL::build('/admin/auth'));
			die();
		} else if(!$user->hasPermission('admincp.forums')){
		  require(ROOT_PATH . '/404.php');
		  die();
        }
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}
 
 
$page = 'admin';
$admin_page = 'forms';
?>
<!DOCTYPE html>
<html lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
  <head>
    <!-- Standard Meta -->
    <meta charset="<?php echo (defined('LANG_CHARSET') ? LANG_CHARSET : 'utf-8'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	
	<?php 
	$title = $language->get('admin', 'admin_cp');
	require(ROOT_PATH . '/core/templates/admin_header.php'); 
	?>
	
	<link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">
  
  </head>

  <body>
  <?php require(ROOT_PATH . '/modules/Core/pages/admin/navbar.php'); ?>
    <div class="container">	
	  <div class="row">
		<div class="col-md-3">
		  <?php require(ROOT_PATH . '/modules/Core/pages/admin/sidebar.php'); ?>
		</div>
		<div class="col-md-9">
		  <div class="card">
		    <div class="card-block">
			  <h3 style="display:inline;"><?php echo $forms_language->get('forms', 'forms'); ?></h3>
			  <?php
				if(isset($_GET['form'])){
					if(!isset($_GET['action'])) {
						// View form
						if(Input::exists()){
							if(Token::check(Input::get('token'))){
								// Validate input
								$validate = new Validate();
								$validation = $validate->check($_POST, array(
									'form_name' => array(
										'required' => true,
										'min' => 2,
										'max' => 150
									),
									'form_url' => array(
										'required' => true,
										'min' => 2,
										'max' => 64
									),
									'form_icon' => array(
										'max' => 255
									)
								));
								
								if($validation->passed()){
									// Create form
									try {
									} catch(Exception $e){
										$error = '<div class="alert alert-danger">Unable to create form: ' . $e->getMessage() . '</div>';
									}
								} else {
									// Error
									$error = '<div class="alert alert-danger">';
									foreach($validation->errors() as $item) {
										if(strpos($item, 'is required') !== false){
											
										} else if(strpos($item, 'minimum') !== false){
											
										} else if(strpos($item, 'maximum') !== false){
											
										}
									}
									$error .= '</div>';
								}
							} else {
								// Invalid token
								$error = '<div class="alert alert-danger">' . $language->get('general', 'invalid_token') . '</div>';
							}
						}
						
						if(!is_numeric($_GET['form'])){
							die();
						} else {
							$form = $queries->getWhere('forms', array('id', '=', $_GET['form']));
						}
						
						// Check if form exist
						if(count($form)){
							if(Input::exists()){
								if(Token::check(Input::get('token'))){
									// Validate input
									$validate = new Validate();
									$validation = $validate->check($_POST, array(
										'form_name' => array(
											'required' => true,
											'min' => 2,
											'max' => 32
										),
										'form_url' => array(
											'required' => true,
											'min' => 2,
											'max' => 32
										),
										'form_icon' => array(
											'max' => 64
										)
									));
									
									if($validation->passed()){
										// Create form
										try {
											// Get link location
											if(isset($_POST['link_location'])){
											  switch($_POST['link_location']){
												case 1:
												case 2:
												case 3:
												case 4:
												  $location = $_POST['link_location'];
												  break;
												default:
												  $location = 1;
											  }
											} else
											$location = 1;
											
											// Can guest visit?
											if(isset($_POST['guest']) && $_POST['guest'] == 'on') $guest = 1;
											else $guest = 0;
										
											// Save to database
											$queries->update('forms', $_GET['form'], array(
												'url' => Output::getClean(rtrim(Input::get('form_url'), '/')),
												'type' => 1,
												'title' => Output::getClean(Input::get('form_name')),
												'guest' => $guest,
												'link_location' => $location,
												'icon' => Input::get('form_icon')
											));
											
											Session::flash('adm-form', '<div class="alert alert-success">' . $language->get('admin', 'successfully_updated') . '</div>');
											Redirect::to(URL::build('/admin/form/', 'form='.$_GET['form']));
											die();
										} catch(Exception $e){
											$error = '<div class="alert alert-danger">Unable to update form: ' . $e->getMessage() . '</div>';
										}
									} else {
										// Error
										$error = '<div class="alert alert-danger">';
										foreach($validation->errors() as $item) {
											if(strpos($item, 'is required') !== false){
												switch($item){
													case (strpos($item, 'form_name') !== false):
														$error .= $forms_language->get('forms', 'input_form_name') . '<br />';
													break;
													case (strpos($item, 'form_url') !== false):
														$error .= $forms_language->get('forms', 'input_form_url') . '<br />';
													break;
												}
											} else if(strpos($item, 'minimum') !== false){
												switch($item){
													case (strpos($item, 'form_name') !== false):
														$error .= $forms_language->get('forms', 'form_name_minimum') . '<br />';
													break;
													case (strpos($item, 'form_url') !== false):
														$error .= $forms_language->get('forms', 'form_url_minimum') . '<br />';
													break;
												}
											} else if(strpos($item, 'maximum') !== false){
												switch($item){
													case (strpos($item, 'form_name') !== false):
														$error .= $forms_language->get('forms', 'form_name_maximum') . '<br />';
													break;
													case (strpos($item, 'form_url') !== false):
														$error .= $forms_language->get('forms', 'form_url_maximum') . '<br />';
													break;
													case (strpos($item, 'form_icon') !== false):
														$error .= $forms_language->get('forms', 'form_icon_maximum') . '<br />';
													break;
												}
											}
										}
										$error .= '</div>';
									}
								} else {
									// Invalid token
									$error = '<div class="alert alert-danger">' . $language->get('general', 'invalid_token') . '</div>';
								}
							}
							$form = $form[0];
							$link_location = $form->link_location;
							?>
							<hr />
							<h5>Editing <?php echo Output::getPurified(htmlspecialchars_decode($form->title)); ?></h5>
							<?php
							if(Session::exists('adm-form')){
								echo Session::flash('adm-form');
							}
							if(isset($error)) echo $error;
							?>
							<form role="form" action="" method="post">
							  <div class="row">
								<div class="col-md-6">
								  <div class="form-group">
									<label for="InputName"><?php echo $forms_language->get('forms', 'form_name'); ?></label>
									<input type="text" name="form_name" class="form-control" id="InputName" placeholder="<?php echo $forms_language->get('forms', 'form_name'); ?>" value="<?php echo Output::getPurified(htmlspecialchars_decode($form->title)); ?>">
								  </div>
								  <div class="form-group">
									<label for="InputUrl"><?php echo $forms_language->get('forms', 'form_url'); ?></label>
									<input type="text" name="form_url" class="form-control" id="InputURL" placeholder="<?php echo $forms_language->get('forms', 'form_url'); ?>" value="<?php echo Output::getPurified(htmlspecialchars_decode($form->url)); ?>">
								  </div>
								  <div class="form-group">
									<label for="inputguest"><?php echo $forms_language->get('forms', 'allow_guests'); ?></label>
									<input id="inputguest" name="guest" type="checkbox" class="js-switch" <?php if($form->guest == 1) echo 'checked '; ?>/>
								  </div>
								</div>
								<div class="col-md-6">
								  <div class="form-group">
									<label for="InputUrl"><?php echo $forms_language->get('forms', 'form_icon'); ?></label>
									<input type="text" name="form_icon" class="form-control" id="InputIcon" placeholder="<?php echo $forms_language->get('forms', 'form_icon'); ?>" value="<?php echo Output::getClean(htmlspecialchars_decode($form->icon)); ?>">
								  </div>
								  <div class="form-group">
									<label for="link_location"><?php echo $forms_language->get('forms', 'link_location'); ?></label>
									<select class="form-control" id="link_location" name="link_location">
									  <option value="1"<?php if($link_location == 1) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_navbar'); ?></option>
									  <option value="2"<?php if($link_location == 2) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_more'); ?></option>
									  <option value="3"<?php if($link_location == 3) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_footer'); ?></option>
									  <option value="4"<?php if($link_location == 4) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_none'); ?></option>
									</select>
								  </div>
								</div>
							  </div>
							  <div class="form-group">
								<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
								<input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
							  </div>
							<form>
							</br>
							<h5 style="display:inline;"><?php echo $forms_language->get('forms', 'fields'); ?></h5>
							<span class="pull-right"><a href="<?php echo URL::build('/admin/form/', 'form='.$form->id.'&amp;action=new'); ?>" class="btn btn-primary"><?php echo $forms_language->get('forms', 'new_field'); ?></a></span>
							<hr>
							<?php
							$fields = $queries->getWhere('forms_fields', array('form_id', '=', $_GET['form']));
							if(count($fields)){
								foreach($fields as $field){
									// Get form type
									switch($field->type){
										case 1:
											$type = $forms_language->get('forms', 'text');
										break;
										case 2:
											$type = $forms_language->get('forms', 'options');
										break;
										case 3:
											$type = $forms_language->get('forms', 'textarea');
										break;
									}
									?>
									<div class="row">
										<div class="col-md-4">
											<?php echo '<a href="' . URL::build('/admin/form/', 'form='.$_GET['form'].'&amp;action=edit&id='.$field->id) . '">' . htmlspecialchars($field->name) . '</a>'; ?>
										</div>
										<div class="col-md-4">
											<?php echo $type ?>
										</div>
										<div class="col-md-4">
											<span class="pull-right">
												<a href="<?php echo URL::build('/admin/form/', 'form='.$_GET['form'].'&amp;action=edit&id='.$field->id); ?>" class="btn btn-warning btn-sm"><span class="fa fa-cogs"></span></a>
												<a href="<?php echo URL::build('/admin/form/', 'form='.$_GET['form'].'&amp;action=delete&amp;id=' . $field->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $forms_language->get('forms', 'confirm_delete_field') ?>')"><span class="fa fa-trash"></span></a>
											</span>
										</div>
									</div>
									<hr>
									<?php 
								}
							} else {
								// None fields defined
								echo '<div class="alert alert-info">' . $forms_language->get('forms', 'none_fields_defined') . '</div>';
							}
						}
					} else {
						if(isset($_GET['action'])) {
							if($_GET['action'] == 'new'){
								// New Field
								if(Input::exists()){
									if(Token::check(Input::get('token'))){
										// Validate input
										$validate = new Validate();
										$validation = $validate->check($_POST, array(
											'field_name' => array(
												'required' => true,
												'min' => 2,
												'max' => 255
											)
										));
										
										if($validation->passed()){
											// Create field
											try {
												// Get field type
												if(isset($_POST['type'])){
												  switch($_POST['type']){
													case 1:
													case 2:
													case 3:
													  $type = $_POST['type'];
													  break;
													default:
													  $type = 1;
												  }
												} else
												$type = 1;
												
												// Is this field required
												if(isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
												else $required = 0;
												
												// Get options into a string
												$options = str_replace("\n", ',', Input::get('options'));
											
												// Save to database
												$queries->create('forms_fields', array(
													'form_id' => $_GET['form'],
													'name' => Output::getClean(Input::get('field_name')),
													'type' => $type,
													'required' => $required,
													'options' => htmlspecialchars($options)
												));
												
												Session::flash('adm-form', '<div class="alert alert-success">' . $forms_language->get('forms', 'field_created_successfully') . '</div>');
												Redirect::to(URL::build('/admin/form/', 'form='.$_GET['form']));
												die();
											} catch(Exception $e){
												$error = '<div class="alert alert-danger">Unable to create field: ' . $e->getMessage() . '</div>';
											}
										} else {
											// Error
											$error = '<div class="alert alert-danger">';
											foreach($validation->errors() as $item) {
												if(strpos($item, 'is required') !== false){
													switch($item){
														case (strpos($item, 'field_name') !== false):
															$error .= $forms_language->get('forms', 'input_field_name') . '<br />';
														break;
													}
												} else if(strpos($item, 'minimum') !== false){
													switch($item){
														case (strpos($item, 'field_name') !== false):
															$error .= $forms_language->get('forms', 'field_name_minimum') . '<br />';
														break;
													}
												} else if(strpos($item, 'maximum') !== false){
													switch($item){
														case (strpos($item, 'field_name') !== false):
															$error .= $forms_language->get('forms', 'field_name_maximum') . '<br />';
														break;
													}
												}
											}
											$error .= '</div>';
										}
									} else {
										// Invalid token
										$error = '<div class="alert alert-danger">' . $language->get('general', 'invalid_token') . '</div>';
									}
								}
								?>
								<hr>
								<h5><?php echo $forms_language->get('forms', 'new_field'); ?></h5>
								<?php if(isset($error)) echo $error; ?>
								<form role="form" action="" method="post">
								  <div class="form-group">
									<label for="InputName"><?php echo $forms_language->get('forms', 'field_name'); ?></label>
									<input type="text" name="field_name" class="form-control" id="InputName" placeholder="<?php echo $forms_language->get('forms', 'field'); ?>">
								  </div>
								  <div class="form-group">
									<label for="type"><?php echo $forms_language->get('forms', 'field_type'); ?></label>
									<select class="form-control" id="type" name="type">
									  <option value="1"><?php echo $forms_language->get('forms', 'text'); ?></option>
									  <option value="2"><?php echo $forms_language->get('forms', 'options'); ?></option>
									  <option value="3"><?php echo $forms_language->get('forms', 'textarea'); ?></option>
									</select>
								  </div>
								  <div class="form-group">
								    <label for="InputOptions"><?php echo $forms_language->get('forms', 'options'); ?> - <?php echo $forms_language->get('forms', 'options_help'); ?></label>
								    <textarea rows="5" class="form-control" name="options" id="options" placeholder="<?php echo $forms_language->get('forms', 'options'); ?>"></textarea>
								  </div>
								  <div class="form-group">
									<label for="Inputrequired"><?php echo $forms_language->get('forms', 'required'); ?></label>
									<input id="inputrequired" name="required" type="checkbox" class="js-switch" />
								  </div>
								  <div class="form-group">
									<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
									<input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
									<a class="btn btn-danger" href="<?php echo URL::build('/admin/form/', 'form='.$_GET['form']); ?>"><?php echo $forms_language->get('forms', 'cancel'); ?></a>
								  </div>
								</form>
								<?php
								
							} else if($_GET['action'] == 'edit'){
								if(isset($_GET['id'])) {
									// Edit Field
									if(Input::exists()){
										if(Token::check(Input::get('token'))){
											// Validate input
											$validate = new Validate();
											$validation = $validate->check($_POST, array(
												'field_name' => array(
													'required' => true,
													'min' => 2,
													'max' => 255
												)
											));
											
											if($validation->passed()){
												// Create field
												try {
													// Get field type
													if(isset($_POST['type'])){
													  switch($_POST['type']){
														case 1:
														case 2:
														case 3:
														  $type = $_POST['type'];
														  break;
														default:
														  $type = 1;
													  }
													} else
													$type = 1;
													
													// Is this field required
													if(isset($_POST['required']) && $_POST['required'] == 'on') $required = 1;
													else $required = 0;
													
													// Get options into a string
													$options = str_replace("\n", ',', Input::get('options'));
												
													// Save to database
													$queries->update('forms_fields', $_GET['id'], array(
														'form_id' => $_GET['form'],
														'name' => Output::getClean(Input::get('field_name')),
														'type' => $type,
														'required' => $required,
														'options' => htmlspecialchars($options)
													));
													
													Session::flash('adm-form', '<div class="alert alert-success">' . $language->get('admin', 'successfully_updated') . '</div>');
													Redirect::to(URL::build('/admin/form/', 'form='.$_GET['form']));
													die();
												} catch(Exception $e){
													$error = '<div class="alert alert-danger">Unable to create field: ' . $e->getMessage() . '</div>';
												}
											} else {
												// Error
												$error = '<div class="alert alert-danger">';
												foreach($validation->errors() as $item) {
													if(strpos($item, 'is required') !== false){
														switch($item){
															case (strpos($item, 'field_name') !== false):
																$error .= $forms_language->get('forms', 'input_field_name') . '<br />';
															break;
														}
													} else if(strpos($item, 'minimum') !== false){
														switch($item){
															case (strpos($item, 'field_name') !== false):
																$error .= $forms_language->get('forms', 'field_name_minimum') . '<br />';
															break;
														}
													} else if(strpos($item, 'maximum') !== false){
														switch($item){
															case (strpos($item, 'field_name') !== false):
																$error .= $forms_language->get('forms', 'field_name_maximum') . '<br />';
															break;
														}
													}
												}
												$error .= '</div>';
											}
										} else {
											// Invalid token
											$error = '<div class="alert alert-danger">' . $language->get('general', 'invalid_token') . '</div>';
										}
									}
									
									if(!is_numeric($_GET['id'])){
										die();
									} else {
										$field = $queries->getWhere('forms_fields', array('id', '=', $_GET['id']));
									}
									
									if(count($field)){
									  $field = $field[0];
									  $type = $field->type;
									  
									  // Get already inputted options
									  if($field->options == null){
										  $options = '';
									  } else {
										  $options = str_replace(',', "\n", htmlspecialchars($field->options));
									  }
									  ?>
									  <hr />
									  <?php if(isset($error)) echo $error; ?>
									  <form role="form" action="" method="post">
										<div class="form-group">
										  <label for="InputName"><?php echo $forms_language->get('forms', 'field_name'); ?></label>
										  <input type="text" name="field_name" class="form-control" id="InputName" placeholder="<?php echo $forms_language->get('forms', 'form_name'); ?>" value="<?php echo htmlspecialchars($field->name); ?>">
										</div>
										<div class="form-group">
										  <label for="type"><?php echo $forms_language->get('forms', 'field_type'); ?></label>
										  <select class="form-control" id="type" name="type">
											<option value="1"<?php if($type == 1) echo ' selected'; ?>><?php echo $forms_language->get('forms', 'text'); ?></option>
											<option value="2"<?php if($type == 2) echo ' selected'; ?>><?php echo $forms_language->get('forms', 'options'); ?></option>
											<option value="3"<?php if($type == 3) echo ' selected'; ?>><?php echo $forms_language->get('forms', 'textarea'); ?></option>
										  </select>
										</div>
									    <div class="form-group">
										  <label for="InputOptions"><?php echo $forms_language->get('forms', 'options'); ?> - <?php echo $forms_language->get('forms', 'options_help'); ?></label>
										  <textarea rows="5" class="form-control" name="options" id="options" placeholder="<?php echo $forms_language->get('forms', 'options'); ?>"><?php echo $options; ?></textarea>
									    </div>
										<div class="form-group">
										  <label for="inputrequired"><?php echo $forms_language->get('forms', 'required'); ?></label>
										  <input id="inputrequired" name="required" type="checkbox" class="js-switch" <?php if($field->required == 1) echo 'checked '; ?>/>
										</div>
									    <div class="form-group">
										  <input type="hidden" name="token" value="<?php echo Token::get();; ?>">
										  <input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
									    </div>
									  </form>
									  <?php
									}
								}
							} else if($_GET['action'] == 'delete'){
								// Delete Field
								if(isset($_GET['id']) && is_numeric($_GET['id'])){
									try {
									  $queries->delete('forms_fields', array('id', '=', $_GET['id']));
									} catch(Exception $e){
									  die($e->getMessage());
									}

									Redirect::to(URL::build('/admin/form/', 'form='.$_GET['form']));
									die();
								}
							}
						}
					}

				}
			  ?>
			</div>
		  </div>
		</div>
      </div>
    </div>
	<?php require(ROOT_PATH . '/modules/Core/pages/admin/footer.php'); ?>
    <?php require(ROOT_PATH . '/modules/Core/pages/admin/scripts.php'); ?>
	
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.js"></script>
	
    <script type="text/javascript">
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	elems.forEach(function(html) {
		var switchery = new Switchery(html);
	});

    <?php if(isset($_GET['form']) || (isset($_GET['action']) && $_GET['action'] == 'new')){ ?>
  	function colourUpdate(that) {
    	var x = that.parentElement;
    	if(that.checked) {
    		x.className = "bg-success";
    	} else {
    		x.className = "bg-danger";
    	}
	}
	$(document).ready(function(){
        $('td').click(function() {
            let checkbox = $(this).find('input:checkbox');
            let id = checkbox.attr('id');

            if(checkbox.is(':checked')){
                checkbox.prop('checked', false);

                colourUpdate(document.getElementById(id));
            } else {
                checkbox.prop('checked', true);

                colourUpdate(document.getElementById(id));
            }
        }).children().click(function(e) {
            e.stopPropagation();
        });
    });
	
	<?php } ?>
    </script>
  </body>
</html>