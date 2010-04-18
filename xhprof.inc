<?php

/**
 * @file
 */

$GLOBALS['xhprof_data_fields'] = array(
  'fn'  => 'Function Name',
  'ct'  => 'Calls',
  'wt'  => 'Incl. Wall Time',
  'cpu' => 'Incl. CPU (us)',
  'mu'  => 'Incl. Mem Use (bytes)',
  'pmu' => 'Incl. Peal Mem Ise (bytes)'
);

function xhprof_table_for_fn($run, $fn) {
  global $xhprof_data_fields;
  
  require_once drupal_get_path('module', 'xhprof') . "/XHProfGraph.class.php";
  
  $sort_column = 'ct';

  $header = array();
  foreach ($xhprof_data_fields as $field => $data) {
    $header[] = array('data' => $data, 'field' => $field);
    if ($data == $_GET['order']) {
      $sort_column = $field;
    }
  }

  $attributes = array("id" => "xhprof-run-details-table");
    
  $graph = new XHProfGraph($run);
  
  $current_node = $graph->findNode($fn);
  $parent_nodes = $graph->findParents($fn);
  $child_nodes = $graph->findChildren($fn);

  XHProfGraph::sortNodes($parent_nodes, $sort_column, $_GET['sort']);
  XHProfGraph::sortNodes($child_nodes, $sort_column, $_GET['sort']);

  $parents = array();
  $parents[] = array('<strong>Parents</strong>');
  foreach ($parent_nodes as $node) {
    $parents[] = $node->asArray();
  }

  $current = array();
  $current[] = array('<strong>This Function</strong>');
  $current[] = $current_node->asArray();

  $children = array();
  $children[] = array('<strong>Children</strong>');
  foreach ($child_nodes as $node) {
    $children[] = $node->asArray();
  }

  $rows = array_merge($parents, $current, $children);
  
  return theme_table($header, $rows, $attributes);
}





