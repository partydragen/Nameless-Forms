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
				if(!isset($_GET['action']) && !isset($_GET['form'])){
					?>
					<span class="pull-right"><a href="<?php echo URL::build('/admin/forms/', 'action=new'); ?>" class="btn btn-primary"><?php echo $forms_language->get('forms', 'new_form'); ?></a></span>
					<hr>
					<?php
					if(Session::exists('adm-forms')){
						echo Session::flash('adm-forms');
					}
					$forms = $queries->orderAll('forms', 'id', 'ASC');
					if(count($forms)){
						?>
						<div class="row">
							<div class="col-md-4">
								<b>Form</b>
							</div>
							<div class="col-md-4">
								<b>Type</b>
							</div>
							<div class="col-md-4">
								<span class="pull-right"><b>Action</b></span>
							</div>
						</div>
						<?php
						foreach($forms as $form){
							// Get form type
							switch($form->type){
								case 1:
									$type = 'Application';
								break;
								case 2:
									$type = 'Form';
								break;
							}
							?>
							<div class="row">
								<div class="col-md-4">
									<?php echo '<a href="' . URL::build('/admin/form/', 'form=' . $form->id) . '">' . htmlspecialchars($form->title) . '</a>'; ?>
								</div>
								<div class="col-md-4">
									<?php echo $type ?>
								</div>
								<div class="col-md-4">
									<span class="pull-right">
										<a href="<?php echo URL::build('/admin/form/', 'form=' . $form->id); ?>" class="btn btn-warning btn-sm"><span class="fa fa-cogs"></span></a>
										<a href="<?php echo URL::build('/admin/forms/', 'action=delete&amp;id=' . $form->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $forms_language->get('forms', 'confirm_delete_form') ?>')"><span class="fa fa-trash"></span></a>
									</span>
								</div>
							</div>
							<hr>
							<?php
						}
					} else {
						// None forms defined
						echo '<div class="alert alert-info">' . $forms_language->get('forms', 'none_forms_defined') . '</div>';
					}
				} else if(isset($_GET['action'])){
					if($_GET['action'] == 'new'){
						// New Form
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
										$queries->create('forms', array(
											'url' => Output::getClean(rtrim(Input::get('form_url'), '/')),
											'type' => 1,
											'title' => Output::getClean(Input::get('form_name')),
											'guest' => $guest,
											'link_location' => $location,
											'icon' => Input::get('form_icon')
										));
										
										Session::flash('adm-forms', '<div class="alert alert-success">' . $forms_language->get('forms', 'form_created_successfully') . '</div>');
										Redirect::to(URL::build('/admin/forms'));
										die();
									} catch(Exception $e){
										$error = '<div class="alert alert-danger">Unable to create form: ' . $e->getMessage() . '</div>';
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
						?>
						<hr>
						<h5><?php echo $forms_language->get('forms', 'creating_new_form'); ?></h5>
						<?php if(isset($error)) echo $error; ?>
							<form role="form" action="" method="post">
							  <div class="row">
								<div class="col-md-6">
								  <div class="form-group">
									<label for="InputName"><?php echo $forms_language->get('forms', 'form_name'); ?></label>
									<input type="text" name="form_name" class="form-control" id="InputName" placeholder="<?php echo $forms_language->get('forms', 'form_name'); ?>">
								  </div>
								  <div class="form-group">
									<label for="InputUrl"><?php echo $forms_language->get('forms', 'form_url'); ?></label>
									<input type="text" name="form_url" class="form-control" id="InputURL" placeholder="<?php echo $forms_language->get('forms', 'form_url'); ?>">
								  </div>
								  <div class="form-group">
									<label for="Inputguest"><?php echo $forms_language->get('forms', 'allow_guests'); ?></label>
									<input id="inputguest" name="guest" type="checkbox" class="js-switch" />
								  </div>
								</div>
								<div class="col-md-6">
								  <div class="form-group">
									<label for="InputUrl"><?php echo $forms_language->get('forms', 'form_icon'); ?></label>
									<input type="text" name="form_icon" class="form-control" id="InputIcon" placeholder="<?php echo $forms_language->get('forms', 'form_icon'); ?>">
								  </div>
								  <div class="form-group">
									<label for="link_location"><?php echo $forms_language->get('forms', 'link_location'); ?></label>
									<select class="form-control" id="link_location" name="link_location">
									  <option value="1"><?php echo $language->get('admin', 'page_link_navbar'); ?></option>
									  <option value="2"><?php echo $language->get('admin', 'page_link_more'); ?></option>
									  <option value="3"><?php echo $language->get('admin', 'page_link_footer'); ?></option>
									  <option value="4"><?php echo $language->get('admin', 'page_link_none'); ?></option>
									</select>
								  </div>
								</div>
							  </div>
							  <div class="form-group">
								<input type="hidden" name="token" value="<?php echo Token::get(); ?>">
								<input type="hidden" name="action" value="update">
								<input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
							  </div>
							<form>
						<?php
					} else if($_GET['action'] == 'delete'){
						// Delete Form
						if(isset($_GET['id']) && is_numeric($_GET['id'])){
							try {
							  $queries->delete('forms', array('id', '=', $_GET['id']));
							  $queries->delete('forms_fields', array('form_id', '=', $_GET['id']));
							} catch(Exception $e){
							  die($e->getMessage());
							}

							Redirect::to(URL::build('/admin/forms'));
							die();
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

    <?php if(isset($_GET['action']) && $_GET['action'] == 'new'){ ?>
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
