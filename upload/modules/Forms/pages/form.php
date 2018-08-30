<?php
/*
 *	Made by Partydragen
 *  https://github.com/partydragen/Nameless-Forms
 *  NamelessMC version 2.0.0-pr4
 *
 *  License: MIT
 *
 *  Form page
 */
 
// Get form info from URL
$form = $queries->getWhere('forms', array('url', '=', rtrim($route, '/')));
if(!count($form)){
    require(ROOT_PATH . '/404.php');
    die();
} else {
    $form = $form[0];
}

// Can guests view?
if($form->guest == 0 && !$user->isLoggedIn()){
	Redirect::to(URL::build('/login/'));
	die();
}

// Always define page name
define('PAGE', 'form-' . $form->id);
?>
<!DOCTYPE html>
<html<?php if(defined('HTML_CLASS')) echo ' class="' . HTML_CLASS . '"'; ?> lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
  <head>
    <!-- Standard Meta -->
    <meta charset="<?php echo (defined('LANG_CHARSET') ? LANG_CHARSET : 'utf-8'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
	<?php 
	$title = $forms_language->get('forms', 'forms');
	require(ROOT_PATH . '/core/templates/header.php');
	?>

  </head>
  <body>
    <?php
	require(ROOT_PATH . '/core/templates/navbar.php');
	require(ROOT_PATH . '/core/templates/footer.php');
	
	// Get fields
	$fields = $queries->getWhere('forms_fields', array('form_id', '=', $form->id));
	$fields_array = array();
	foreach($fields as $field){
		$options = explode(',', Output::getClean($field->options));
		$fields_array[] = array(
			'id' => Output::getClean($field->id),
			'name' => Output::getClean($field->name),
			'type' => Output::getClean($field->type),
			'options' => $options,
		);
	}
	
	$smarty->assign(array(
		'TITLE' => Output::getClean($form->title),
		'FIELDS' => $fields_array,
		'TOKEN' => Token::get(),
		'SUBMIT' => $language->get('general', 'submit')
	));
	
	// Display template
	$smarty->display(ROOT_PATH . '/custom/templates/' . TEMPLATE . '/forms/form.tpl');
	require(ROOT_PATH . '/core/templates/scripts.php');
	?>
  </body>
</html>