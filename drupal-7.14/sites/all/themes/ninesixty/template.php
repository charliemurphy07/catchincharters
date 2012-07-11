<?php
// $Id: template.php,v 1.1.2.6.4.2 2011/01/11 01:08:49 dvessel Exp $

/**
 * The default group for NineSixty framework CSS files added to the page.
 */
define('CSS_NS_FRAMEWORK', -200);

/**
 * Implements hook_preprocess_html
 */
function ninesixty_preprocess_html(&$vars) {
  $vars['classes_array'][] = 'show-grid';
}

/**
 * Preprocessor for page.tpl.php template file.
 */
function ninesixty_preprocess_page(&$vars, $hook) {
  // For easy printing of variables.
  $vars['logo_img'] = '';
  if (!empty($vars['logo'])) {
    $vars['logo_img'] = theme('image', array(
      'path'  => $vars['logo'],
      'alt'   => t('Home'),
      'title' => t('Home'),
    ));
  }
  $vars['linked_logo_img']  = '';
  if (!empty($vars['logo_img'])) {
    $vars['linked_logo_img'] = l($vars['logo_img'], '<front>', array(
      'attributes' => array(
        'rel'   => 'home',
        'title' => t('Home'),
      ),
      'html' => TRUE,
    ));
  }
  $vars['linked_site_name'] = '';
  if (!empty($vars['site_name'])) {
    $vars['linked_site_name'] = l($vars['site_name'], '<front>', array(
      'attributes' => array(
        'rel'   => 'home',
        'title' => t('Home'),
      ),
    ));
  }

  // Site navigation links.
  $vars['main_menu_links'] = '';
  if (isset($vars['main_menu'])) {
    $vars['main_menu_links'] = theme('links__system_main_menu', array(
      'links' => $vars['main_menu'],
      'attributes' => array(
        'id' => 'main-menu',
        'class' => array('inline', 'main-menu'),
      ),
      'heading' => array(
        'text' => t('Main menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    ));
  }
  $vars['secondary_menu_links'] = '';
  if (isset($vars['secondary_menu'])) {
    $vars['secondary_menu_links'] = theme('links__system_secondary_menu', array(
      'links' => $vars['secondary_menu'],
      'attributes' => array(
        'id'    => 'secondary-menu',
        'class' => array('inline', 'secondary-menu'),
      ),
      'heading' => array(
        'text' => t('Secondary menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    ));
  }

}

/**
 * Implements hook_preprocess_node
 */
function ninesixty_preprocess_node(&$vars) {
	// We need to check quantity for all product types
	if($vars['node']->type == 'product'){
		// We will first check base stock levels.
		$stock = uc_stock_level($vars['node']->model);
		if($stock < 0){
			// hide add to cart button show sold out.
			$vars['content']['add_to_cart']['#form']['actions']['submit']['#value'] = 'SOLD OUT';
			$vars['content']['add_to_cart']['#form']['actions']['submit']['#attributes']['class'][] = 'disable';
			// We add some inline js, I know, I know inline js... However it provides us a diabled submit button.
			drupal_add_js('jQuery(document).ready(function(){
				jQuery(".node-add-to-cart.disable").attr("disabled","disabled");
			});', 'inline');
		}		
	}
}

function ninesixty_form_alter(&$form, &$form_state, $form_id){ 
	if(preg_match('/^uc_product_add_to_cart_form/', $form_id)){
		$form['#submit'][] = 'ninesixty_stock_check';
	}
	if($form_id == 'uc_cart_view_form') {
		$cid = uc_cart_get_id(FALSE);
		$efq = new EntityFieldQuery();
		$result = $efq->entityCondition('entity_type', 'uc_cart_item')
        	->propertyCondition('cart_id', $cid)
        	->propertyOrderBy('cart_item_id', 'ASC')
        	->execute();
        if (!empty($result['uc_cart_item'])) {
        	$items[$cid] = entity_load('uc_cart_item', array_keys($result['uc_cart_item']), NULL, TRUE);

        	foreach ($items[$cid] as $item) {
	          $stock = uc_stock_level($item->model);
	          if($item->qty > $stock){
	          	  $message = 'We are sorry but we only have '. $stock .' spot(s) available for '. $item->title .' trip. Please adjust quantity.';
		          drupal_set_message($message, 'error');
		          // We add some inline js, I know, I know inline js... However it provides us a diabled submit button.
		          drupal_add_js('jQuery(document).ready(function(){
					jQuery("#edit-checkout--2").attr("disabled","disabled");
				  });', 'inline');
				  return FALSE;
	          }
	          else {
		          drupal_get_messages('error');
	          }
	        }
	    }
	}
}

/**
 * We use this function to check stock. If we do not have enough stock aviable we show an error message to the user.
 */
function ninesixty_stock_check(){
	$cid = uc_cart_get_id(FALSE);
	$efq = new EntityFieldQuery();
    $result = $efq->entityCondition('entity_type', 'uc_cart_item')
        ->propertyCondition('cart_id', $cid)
        ->propertyOrderBy('cart_item_id', 'ASC')
        ->execute();
    if (!empty($result['uc_cart_item'])) {
        $items[$cid] = entity_load('uc_cart_item', array_keys($result['uc_cart_item']), NULL, TRUE);

        foreach ($items[$cid] as $item) {
          $stock = uc_stock_level($item->model);
          if($item->qty > $stock){
          	  $message = 'We are sorry but we only have '. $stock .' spot(s) aviable for '. $item->title .' trip. Please adjust quanity.';
	          drupal_set_message($message, 'error');
          }
        }
     }
}
/**
 * Contextually adds 960 Grid System classes.
 *
 * The first parameter passed is the *default class*. All other parameters must
 * be set in pairs like so: "$variable, 3". The variable can be anything available
 * within a template file and the integer is the width set for the adjacent box
 * containing that variable.
 *
 *  class="<?php print ns('grid-16', $var_a, 6); ?>"
 *
 * If $var_a contains data, the next parameter (integer) will be subtracted from
 * the default class. See the README.txt file.
 */
function ns() {
  $args = func_get_args();
  $default = array_shift($args);
  // Get the type of class, i.e., 'grid', 'pull', 'push', etc.
  // Also get the default unit for the type to be procesed and returned.
  list($type, $return_unit) = explode('-', $default);

  // Process the conditions.
  $flip_states = array('var' => 'int', 'int' => 'var');
  $state = 'var';
  foreach ($args as $arg) {
    if ($state == 'var') {
      $var_state = !empty($arg);
    }
    elseif ($var_state) {
      $return_unit = $return_unit - $arg;
    }
    $state = $flip_states[$state];
  }

  $output = '';
  // Anything below a value of 1 is not needed.
  if ($return_unit > 0) {
    $output = $type . '-' . $return_unit;
  }
  return $output;
}

/**
 * Implements hook_css_alter.
 * 
 * This rearranges how the style sheets are included so the framework styles
 * are included first.
 *
 * Sub-themes can override the framework styles when it contains css files with
 * the same name as a framework style. This mirrors the behavior of the 6--1
 * release of NineSixty warts and all. Future versions will make this obsolete.
 */
function ninesixty_css_alter(&$css) {
  global $theme_info, $base_theme_info;

  // Dig into the framework .info data.
  $framework = !empty($base_theme_info) ? $base_theme_info[0]->info : $theme_info->info;

  // Ensure framework CSS is always first.
  $on_top = CSS_NS_FRAMEWORK;

  // Pull framework styles from the themes .info file and place them above all stylesheets.
  if (isset($framework['stylesheets'])) {
    foreach ($framework['stylesheets'] as $media => $styles_from_960) {
      foreach ($styles_from_960 as $style_from_960) {
        // Force framework styles to come first.
        if (strpos($style_from_960, 'framework') !== FALSE) {
          $css[$style_from_960]['group'] = $on_top;
          // Handle styles that may be overridden from sub-themes.
          foreach (array_keys($css) as $style_from_var) {
            if ($style_from_960 != $style_from_var && basename($style_from_960) == basename($style_from_var)) {
              $css[$style_from_var]['group'] = $on_top;
            }
          }
        }
      }
    }
  }
}
