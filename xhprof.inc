<?php

/**
 * @file
 */

$GLOBALS['xhprof_sort_col'] = 'ct';
$GLOBALS['xhprof_sort_order'] = $_GET['sort'];

function xhprof_table_for_method($run, $method = "main()") {
  $header = array(
    array("data" => "Function Name", 'field' => 'fn'),
    array("data" => "Calls", 'field' => 'ct'),
    array("data" => "Incl. Wall Time<br>(us)", 'field' => 'wt'),
    array("data" => "Incl. CPU<br>(us)", 'field' => 'cpu'),
    array("data" => "Incl.<br>MemUse<br>(bytes)", 'field' => 'mu'),
    array("data" => "Incl.<br> PeakMemUse<br>(bytes)", 'field' => 'pmu')
  );
  
  $attributes = array("id" => "xhprof-run-details-table");
  
  // KLUDGE
  xhprof_sort_field($header);
  
  $children = array();
  foreach ($run->data as $call => $info) {
    list($parent, $child) = explode('==>', $call);
    if ($parent === $method and $child) {
      $children[$child] = $info;
    }
  }
  
  $rows = array();
  foreach ($children as $child => $info) {
    $rows[] = array(
      'fn' =>  l($child, 'admin/xhprof/run/'.$run->run_id.'/'.$child),
      'ct' => $info['ct'],
      'wt' => $info['wt'],
      'cpu' => $info['cpu'],
      'mu' => $info['mu'],
      'pmu' => $info['pmu']
    );
  }
  
  usort($rows, "xhprof_sort_run_data");
  
  return theme_table($header, $rows, $attributes);
}

function xhprof_sort_run_data($a, $b) {
  global $xhprof_sort_col;
  global $xhprof_sort_order;
  
  if ($a[$xhprof_sort_col] == $b[$xhprof_sort_col]) {
    return 0;
  }

  if ($xhprof_sort_order === 'desc') {
    return ($a[$xhprof_sort_col] > $b[$xhprof_sort_col]) ? -1 : 1;
  } else {
    return ($a[$xhprof_sort_col] > $b[$xhprof_sort_col]) ? 1 : -1;
  }
}

function xhprof_sort_field($header) {
  foreach ($header as $h) {
    if ($h['data'] === $_GET['order']) {
      global $xhprof_sort_col;
      $xhprof_sort_col = $h['field'];
    }
  }
}


