<?php

/**
 * Implements hook_js_alter().
 */
function mattias_js_alter(&$javascript) {
  // Unset module javascripts.
  unset($javascript['misc/tableheader.js']);
}

/**
 * Change the default meta content-type tag to the shorter HTML5 version.
 */
function mattias_html_head_alter(&$head_elements) {
  // Unset meta variables.
  unset($head_elements['system_meta_canonical']);
  unset($head_elements['system_meta_generator']);
    foreach ($head_elements as $key => $element) {
    if (isset($element['#attributes']['rel']) && $element['#attributes']['rel'] == 'canonical') {
      unset($head_elements[$key]);
    }
    if (isset($element['#attributes']['rel']) && $element['#attributes']['rel'] == 'shortlink') {
      unset($head_elements[$key]);
    }
    $head_elements['system_meta_content_type']['#attributes'] = array(
      'charset' => 'utf-8',
    );
  }
}

/**
 * Preprocess magic.
 */
function mattias_preprocess(&$vars, $hook) {
  if (!empty($vars['page']['content']['system_main'])) {
    $vars['page']['content']['system_main']['#theme_wrappers'] = array_diff($vars['page']['content']['system_main']['#theme_wrappers'], array('block'));
  }
  if($hook == 'page' && arg(0)=='user') {
    $vars['tabs'] = "";
    $vars['title'] = "";
  }
  return $vars;
}

/**
 * Process variables for the html tag.
 */
function mattias_process_html_tag(&$vars) {
  $tag = &$vars['element'];
  if ($tag['#tag'] == 'style' || $tag['#tag'] == 'script') {
    // Remove redundant type attribute and CDATA comments.
    unset($tag['#attributes']['type'], $tag['#value_prefix'], $tag['#value_suffix']);

    // Remove media="all" but leave others unaffected.
    if (isset($tag['#attributes']['media']) && $tag['#attributes']['media'] === 'all') {
      unset($tag['#attributes']['media']);
    }
  }
}

/**
 * Process variables for html.
 */
function mattias_preprocess_html(&$variables) {
  global $language;

  // Attributes for html element.
  $variables['html_attributes'] = 'lang="' . $language->language . '" dir="' . $language->dir . '"';
}

/**
 * Add meta tags.
 */
function mattias_page_alter($page) {
  $meta_viewport = array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'name' => 'viewport',
      'content' => 'width=device-width, initial-scale=1'
    )
  );
  drupal_add_html_head( $meta_viewport, 'meta_viewport' );
}

/**
 * Override of theme_field().
 */
function mattias_field($vars) {
  $output = '';
  return $output;
}

function mattias_field__field_preamble($vars) {
  $output = '';
  
  foreach ($vars['items'] as $delta => $item) {
    $output .= '<h6>' . drupal_render($item) . '</h6>';
  }
  
  return $output;
}

function mattias_field__field_image($vars) {
  $output = '';

  foreach ($vars['items'] as $delta => $item) {
    $output .= '<figure>' . drupal_render($item) . '</figure>';
  }

  return $output;
}

/**
 * Override of theme_menu_local_tasks().
 */
function mattias_menu_local_tasks(&$variables) {
  $output = '';
  $output .= '<nav>';

  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<ul>';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<ul>';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }
  
  $output .= '</nav>';
  return $output;
}

/**
 * Override of theme_menu_local_task().
 */
function mattias_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];

  if (empty($link['localized_options']['html'])) {
    $link['title'] = check_plain($link['title']);
  }
  $link['localized_options']['html'] = TRUE;
  $link_text = t('!local-task-title', array('!local-task-title' => $link['title']));

  return '<li>' . l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Override of theme_menu_link().
 */
function mattias_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';
  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li>' . $output . $sub_menu . "</li>\n";
}

/**
 * Change titles for user pages.
 */
function mattias_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'user_register_form') {
    $title = drupal_set_title('Sign up, will you?');
    drupal_set_title('');
    $form['account']['#prefix'] = '<h1>' . $title . '</h1>';
    $form['account']['mail']['#title'] = t('Email');
    $form['actions']['submit']['#value'] = t('Create your new account');
    unset($form['account']['name']['#description']);
    unset($form['account']['mail']['#description']);
  }
  elseif ($form_id == 'user_pass') {
    $title = drupal_set_title('Short memory?');
    drupal_set_title('');
    $form['name']['#prefix'] = '<h1>' . $title . '</h1>';
  }
  elseif ($form_id == 'user_login') {
    $title = drupal_set_title('Dive right into it!');
    drupal_set_title('');
    $form['name']['#prefix'] = '<h1>' . $title . '</h1>';
    $form['actions']['#suffix'] = l(t('Forgot your password?'), 'user/password', array('attributes' => array('class' => array('forgot-password'))));
    unset($form['name']['#description']);
    unset($form['pass']['#description']);
  }
}

/**
 * Override of theme_form_element().
 */
function mattias_form_element($variables) {
  $element = &$variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  //if (!empty($element['#type'])) {
    //$attributes['class'][] = strtr($element['#type'], '_', '-');
  //}
  if (!empty($element['#name'])) {
    $attributes['class'][] = strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<small>' . $element['#description'] . "</small>\n";
  }

  $output .= "</div>\n";

  return $output;
}

/**
 * Override of theme_container().
 */
function mattias_container($variables) {
  $element = $variables['element'];
  return $element['#children'];
}

/**
 * Override of theme_form().
 */
function mattias_form($variables) {
  $element = $variables['element'];
  if (isset($element['#action'])) {
    $element['#attributes']['action'] = drupal_strip_dangerous_protocols($element['#action']);
  }
  element_set_attributes($element, array('method', 'id'));
  if (empty($element['#attributes']['accept-charset'])) {
    $element['#attributes']['accept-charset'] = "UTF-8";
  }
  return '<form' . drupal_attributes($element['#attributes']) . '>'  . $element['#children'] . '</form>';
}

/**
 * Customize user profile.
 */
function mattias_preprocess_user_profile(&$variables) {
  $account = $variables['elements']['#account'];

  foreach (element_children($variables['elements']) as $key) {
    $variables['user_profile'][$key] = $variables['elements'][$key];
  }
  $variables['user_profile']['mail'] = $account->mail;
  $variables['user_profile']['name'] = $account->name;

  field_attach_preprocess('user', $account, $variables['elements'], $variables);
}

/**
 * Override of theme_form_required_marker().
 */
function mattias_form_required_marker($variables) {
  return '';
}

/**
 * Override of theme_links().
 */
function mattias_links($variables) {
  $links = $variables['links'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';
    $output .= '<ul>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = array($key);

      $output .= '<li>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      $i++;
      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }
  return $output;
}

/**
 * Override of theme_fieldset().
 */
function mattias_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));
  _form_set_class($element, array('form'));

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    $output .= '<h5>' . $element['#title'] . '</h5>';
  }
  if (!empty($element['#description'])) {
    $output .= '<small>' . $element['#description'] . '</small>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= "</fieldset>\n";
  return $output;
}

/**
 * Override of theme_status_messages().
 */
function mattias_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  foreach (drupal_get_messages($display) as $type => $messages) {
    $output .= "<div>\n";
    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }
    $output .= "</div>\n";
  }
  return $output;
}

function mattias_preprocess_block(&$variables) {
  $variables['block_html_id'] = drupal_html_id($variables['block']->module . '-' . $variables['block']->delta);
}

/**
 * Preprocess node().
 */
function mattias_preprocess_node(&$variables) {
	$variables['submitted'] = t('!datemonth !dateday, !dateyear', 
		array(
		'!dateday' => date("j", $variables['created']),
		'!datemonth' => date("F", $variables['created']),
		'!dateyear' => date("Y", $variables['created'])
  ));
}

/**
 * Implements hook_form_node_form_alter().
 */
function mattias_form_node_form_alter(&$form, &$form_state) {
  $form['#after_build'][] = 'mattias_node_form_after_build';

  $form['author']['#access'] = FALSE;
  $form['menu']['#access'] = FALSE;
  $form['options']['#access'] = FALSE;
  $form['revision_information']['#access'] = FALSE;
  $form['field_image']['und'][0]['#description'] ='';
  $form['field_image']['#description'] ='';
  $form['additional_settings']['#type'] = 'fieldset';
  $form['additional_settings']['#collapsible'] = FALSE;
  $form['actions']['submit']['#value'] = t('Publish');
  $form['path']['pathauto']['#description'] ='';
  $form['path']['alias']['#description'] ='';
  
  $form['field_image']['und']['#description'] ='';
  //print_r($form);
  unset($form['actions']['preview']);
}

function mattias_node_form_after_build($form) {
  unset($form[LANGUAGE_NONE][0]['format']);
  unset($form[LANGUAGE_NONE][0]['#description']);
  $form['field_image']['und']['#description'] ='';
  $form['body']['und']['0']['format']['#access'] = FALSE;
  return $form;
}

