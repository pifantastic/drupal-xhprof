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
  
  $parents = array();
  $parents[] = array('Parents', '', '', '', '', '', '');
  foreach ($graph->findParents($method) as $node) {
    $parents[] = array(
      'fn' =>  l($node->method, 'admin/xhprof/run/'.$run->run_id.'/'.$node->method),
      'ct' => $node->data['ct'],
      'wt' => $node->data['wt'],
      'cpu' => $node->data['cpu'],
      'mu' => $node->data['mu'],
      'pmu' => $node->data['pmu']
    );
  }

  $children = array();
  $children[] = array('Children', '', '', '', '', '', '');
  foreach ($graph->findConnections($method) as $node) {
    $children[] = array(
      'fn' =>  l($node->method, 'admin/xhprof/run/'.$run->run_id.'/'.$node->method),
      'ct' => $node->data['ct'],
      'wt' => $node->data['wt'],
      'cpu' => $node->data['cpu'],
      'mu' => $node->data['mu'],
      'pmu' => $node->data['pmu']
    );
  }

  $rows = array_merge($parents, $children);
  
  return theme_table($header, $rows, $attributes);
}





