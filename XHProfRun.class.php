<?php

require_once 'XHProfGraph.class.php';

class XHProfRun {
  
  public static $sortDirection = "asc";
  public static $sortColumn = "ct";

  public $id = 0;
  public $data = array();
  public $url = '';
  public $functions = array();
  
  public function __construct($id_or_data, $url = "") {
    if (is_array($id_or_data)) {
      $this->data = $id_or_data;
      $this->url = $url;
      $query = "INSERT INTO {xhprof_runs} (url, data, created) VALUES ('%s', '%s', '%s')";
      $args = array($this->url, serialize($this->data), time());
      db_query($query, $args);
    }
    else {
      $result = db_query("SELECT * FROM {xhprof_runs} WHERE id = %d", $id_or_data);
      $row = db_fetch_object($result);
      
      if ($row !== FALSE) {
        $this->id = $row->id;
        $this->data = unserialize($row->data);
        $this->url = $row->url;
        $this->created = $row->created;
      }
    }
  
    foreach ($this->data as $call => $info) {
      list($parent, $child) = explode('==>', $call);
      $this->functions[$call] = new XHProfFunction($this->id, $parent, $child, $info['ct'], $info['wt'], $info['mu'], $info['pmu'], $info['cpu']);
    }
  }

  public function __get($name) {
    switch($name) {
      case 'time':
        return $this->data['main()']['wt'];
      case 'calls':
        $field = 'ct';
        break;
      case 'memory':
        $field = 'mu';
        break;
      case 'cpu':
        $field = 'cpu';
        break;
    }

    $total = 0;    
    foreach ($this->functions as $function) {
      $total += $function->$field;
    }    
    return $total;
  }

  public function getParents($fn) {
    $parents = array();
    foreach ($this->functions as $function) {
      if ($function->child === $fn) {
        $parents[$function->parent] = $function;
      }
    }
    $this->sort($parents);
    return $parents;
  }

  public function getChildren($fn) {
    $children = array();
    foreach ($this->functions as $function) {
      if ($function->parent === $fn) {
        $children[$function->child] = $function;
      }
    }
    $this->sort($children);
    return $children;
  }

  public function sort(&$data) {
    usort($data, array($this, "__cmp"));
  }

  public function __cmp($a, $b) {
    if (!isset($a->{self::$sortColumn}) and !isset($b->{self::$sortColumn})) {
      return 0;
    }
    
    if ($a->{self::$sortColumn} == $b->{self::$sortColumn}) {
      return 0;
    }

    if (strtolower(self::$sortDirection) == "asc") {
      return ($a->{self::$sortColumn} > $b->{self::$sortColumn}) ? 1 : -1;
    } 
    else {
      return ($a->{self::$sortColumn} > $b->{self::$sortColumn}) ? -1 : 1;
    }
  }
}

class XHProfFunction {
  public $id;  
  public $parent;
  public $child;
  public $ct;
  public $wt;
  public $mu;
  public $pmu;
  public $cpu;

  public function __construct($id, $parent, $child, $ct, $wt, $mu, $pmu, $cpu) {
    $this->id = $id;    
    $this->parent = $parent;
    $this->child = $child;
    $this->ct = $ct;
    $this->wt = $wt;
    $this->mu = $mu;
    $this->pmu = $pmu;
    $this->cpu = $cpu;
  }

  public function asArray() {
    global $xhprof_data_fields;
    $array = array();    
    foreach ($xhprof_data_fields as $field => $name) {
      $array[$field] = $this->$field;
    }
    return $array;
  }
}

