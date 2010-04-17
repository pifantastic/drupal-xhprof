<?php

/**
 * @file
 */

$GLOBALS['xhprof_sort_col'] = 'ct';
$GLOBALS['xhprof_sort_order'] = isset($_GET['sort']) ? $_GET['sort'] : 'desc';

function xhprof_table_for_method($run, $method) {
  require_once drupal_get_path('module', 'xhprof') . "/XHProfGraph.class.php";

  $header = array(
    array("data" => "Function Name", 'field' => 'fn'),
    array("data" => "Calls", 'field' => 'ct'),
    array("data" => "Incl. Wall Time<br>(us)", 'field' => 'wt'),
    array("data" => "Incl. CPU<br>(us)", 'field' => 'cpu'),
    array("data" => "Incl.<br>MemUse<br>(bytes)", 'field' => 'mu'),
    array("data" => "Incl.<br> PeakMemUse<br>(bytes)", 'field' => 'pmu')
  );
  
  $attributes = array("id" => "xhprof-run-details-table");
    
  $graph = new XHProfGraph($run);
  
  $current_node = $graph->nodeForMethod($method);
  $parent_nodes = $graph->findParents($method);
  $child_nodes = $graph->findChildren($method);
  
  XHProfGraph::sortNodes($parent_nodes, 'ct', $_GET['sort']);
  XHProfGraph::sortNodes($child_nodes, 'ct', $_GET['sort']);

  $parents = array();
  $parents[] = array('Parents', '', '', '', '', '', '');
  foreach ($parent_nodes as $node) {
    $parents[] = array(
      'fn' =>  l($node->method, 'admin/xhprof/run/'.$run->run_id.'/'.$node->method),
      'ct' => $node->data['ct'],
      'wt' => $node->data['wt'],
      'cpu' => $node->data['cpu'],
      'mu' => $node->data['mu'],
      'pmu' => $node->data['pmu']
    );
  }

  $current = array();
  $current[] = array('This Function', '', '', '', '', '', '');
  $current[] = array(
    'fn' =>  l($current_node->method, 'admin/xhprof/run/'.$run->run_id.'/'.$current_node->method),
    'ct' => $current_node->data['ct'],
    'wt' => $current_node->data['wt'],
    'cpu' => $current_node->data['cpu'],
    'mu' => $current_node->data['mu'],
    'pmu' => $current_node->data['pmu']
  );

  $children = array();
  $children[] = array('Children', '', '', '', '', '', '');
  foreach ($child_nodes as $node) {
    $children[] = array(
      'fn' =>  l($node->method, 'admin/xhprof/run/'.$run->run_id.'/'.$node->method),
      'ct' => $node->data['ct'],
      'wt' => $node->data['wt'],
      'cpu' => $node->data['cpu'],
      'mu' => $node->data['mu'],
      'pmu' => $node->data['pmu']
    );
  }
  
  $rows = array_merge($parents, $current, $children);
  
  return theme_table($header, $rows, $attributes);
}





