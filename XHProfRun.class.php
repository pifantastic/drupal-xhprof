<?php

require_once 'XHProfGraph.class.php';

class XHProfRun {
  
  public $id;
  public $data;
  public $graph;

  public function __construct($run_id, $source = XHPROF_SOURCE) {
    $result = db_query("SELECT * FROM {xhprof_runs} WHERE run_id = %d", $run_id);
    $run = db_fetch_object($result);
    
    if ($run !== FALSE) {
      $this->id = $run->run_id;
      $this->data = unserialize($run->data);
      $this->graph = new XHProfGraph($this->id, $this->data);
    }
  }

}
