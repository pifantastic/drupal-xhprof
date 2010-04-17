<?php

class XHProfGraph {
  
  public $run;  
  
  public $nodes = array();
  public $edges = array();
  
  public static $sortDirection = "asc";
  public static $sortColumn = "ct";

  public function __construct($run) {
    $this->run = $run;

    foreach ($run->data as $call => $data) {
      list($parent_fn, $child_fn) = explode('==>', $call);
      
      $parent_node = new XHProfNode($parent_fn, $data, $run->run_id);
      $this->addNode($parent_node);
      
      if ($child_fn) {
        $child_node = new XHProfNode($child_fn, $data, $run->run_id);
        $this->addNode($child_node);
        
        $edge = new XHProfEdge($parent_node, $child_node);
        $this->addEdge($edge);
      }
    }
  }

  public function addNode(XHProfNode $node) {
    if (!$this->findNode($node)) {
      $this->nodes[] = $node;
    }
  }

  public function addEdge(XHProfEdge $edge) {
    if (!$this->findNode($edge->node1)) {
      $this->addNode($edge->node1);
    }
    
    if (!$this->findNode($edge->node2)) {
      $this->addNode($edge->node2);
    }    
    
    if (!$this->findEdge($edge)) {
      $this->edges[] = $edge;
    }
  }

  public function findNode($node) {
    foreach ($this->nodes as $n) {
      if ($node->fn == $n->fn) {
        return $n;
      }
    }
    
    return FALSE;
  }

  public function findEdge($edge) {
    foreach ($this->edges as $e) {
      if ($e->node1->fn == $edge->node1->fn and $e->node2->fn == $edge->node2->fn) {
        return $e;
      }
    }
    
    return FALSE;
  }

  public function findChildren($fn = "main()") {
    $connections = array();
    foreach ($this->edges as $edge) {
      if ($edge->node1->fn == $fn) {
        $connections[] = $edge->node2;
      }
    }
    return $connections;
  }
  
  public function findParents($fn = "main()") {
    $parents = array();    
    foreach ($this->edges as $edge) {
      if ($edge->node2->fn == $fn) {
        $parents[] = $edge->node1;
      }
    }
    return $parents;
  }

  public function nodeForFn($fn) {
    foreach ($this->nodes as $node) {
      if ($node->fn == $fn) {
        return $node;
      }
    }

    return FALSE;
  }
  
  public static function sortNodes(&$nodes = array(), $column, $direction = "asc") {
    self::$sortDirection = $direction;
    self::$sortColumn = $column;
    usort($nodes, "XHProfGraph::compareNodes");
  }

  public static function compareNodes($a, $b) {
    if ($a->{self::$sortColumn} == $b->{self::$sortColumn}) {
      return 0;
    }

    if (self::$sortDirection == "asc") {
      return ($a->{self::$sortColumn} > $b->{self::$sortColumn}) ? 1 : -1;
    } 
    else {
      return ($a->{self::$sortColumn} > $b->{self::$sortColumn}) ? -1 : 1;
    }
  }

}

class XHProfNode {

  public $run_id;
  public $fn = '';
  public $ct = 0;
  public $wt = 0;
  public $cpu = 0;
  public $mu = 0;
  public $pmu = 0;

  public $calculated_data = array();

  public function __construct($fn, $data, $run_id = 0) {
    $this->run_id = $run_id;    
    $this->fn = $fn;
    foreach ($data as $key => $value) {
      $this->$key = $value;    
    }
  }

  public function asArray() {
    global $xhprof_data_fields;
    $array = array();    
    foreach ($xhprof_data_fields as $field => $name) {
      switch ($field) {
        case 'fn':
          $array['fn'] = l($this->fn, "admin/xhprof/run/{$this->run_id}/{$this->fn}");
          break;
        default:
          $array[$field] = $this->$field;
      }
    }
    return $array;
  }
  
  public function __get($name) {
    if (!isset($this->calculated_data[$name])) {
      return NULL;    
    }    
    return $this->calculated_data[$name];
  }
}

class XHProfEdge {

  public $node1;
  public $node2;

  public function __construct($node1, $node2) {
    $this->node1 = $node1;
    $this->node2 = $node2;
  }
}


