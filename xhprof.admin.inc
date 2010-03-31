<?php
// $Id$

/**
 * @file
 * Administration page callbacks for the xhprof module
 */

/**
 * Form builder. Configure annotations.
 *
 * @ingroup forms
 * @see system_settings_form().
 */
function xhprof_admin_settings() {
  $form['xhprof_foo'] = array( 
    '#type' => 'textfield', 
    '#size' => 10,
    '#title' => t('Foo'), 
    '#default_value' => variable_get('xhprof_foo', 5), 
    '#description' => t('This is just a placeholder for now.'), 
  );

  return system_settings_form($form);
}

function xhprof_runs_table() {
  $form['xhprof_foo'] = array( 
    '#type' => 'textfield', 
    '#size' => 10,
    '#title' => t('Foo'), 
    '#default_value' => variable_get('xhprof_foo', 5), 
    '#description' => t('This is just a placeholder for now.'), 
  );
  
  krumo(system_settings_form($form));
  
  return system_settings_form($form);
}