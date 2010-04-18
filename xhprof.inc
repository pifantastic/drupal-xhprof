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

function xhprof_table_for_fn($run, $fn = "main()") {
  global $xhprof_data_fields;
  $sort_column = 'ct';

  // Table attributes
  $attributes = array("id" => "xhprof-run-details-table");

  // Table header
  $header = array();
  foreach ($xhprof_data_fields as $field => $data) {
    $header[] = array('data' => $data, 'field' => $field);

    // Silly KLUDGE to set the 
    if ($data == $_GET['order']) {
      $sort_column = $field;
    }
  }
    
  // Get current, child and parent nodes for this function
  $current_node = $run->graph->findNode($fn);
  $parent_nodes = $run->graph->findParents($fn);
  $child_nodes = $run->graph->findChildren($fn);
  
  // Sort nodes using $_GET['sort']
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

