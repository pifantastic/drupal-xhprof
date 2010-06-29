<?php

class XHProfGraph {
  
  public $run_id;  
  public $run_data;

  public $nodes = array();
  public $edges = array();
  
  public static $sortDirection = "asc";
  public static $sortColumn = "ct";

  public function __construct($run_data) {
    $this->run_data = $run_data;

    foreach ($run_data as $call => $data) {
      list($parent_fn, $child_fn) = explode('==>', $call);
      
      $parent_node = new XHProfNode($parent_fn, $data);
      $this->addNode($parent_node);
      
      if ($child_fn) {
        $child_node = new XHProfNode($child_fn, $data);
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
    $fn = (is_object($node)) ? $node->fn : $node;
    foreach ($this->nodes as $n) {
      if ($n->fn == $fn) {
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
    $children = array();
    foreach ($this->edges as $edge) {
      if ($edge->node1->fn == $fn) {
        $children[] = $edge->node2;
      }
    }
    return $children;
  }

  // I don't know if this makes any sense yet
  public function findSiblings($fn = "main()") {
    $parents = $this->findParents($fn);
    $siblings = array();

    if (count($parents) == 0) {
      return $siblings;
    }
    
    foreach ($this->edges as $edge) {
      foreach ($parents as $parent) {
        if ($parent->fn == $edge->node1->fn) {
          $siblings[$edge->node2->fn] = $edge->node2;
        }
      }
    }
    return $siblings;
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
  
  public static function sortNodes(&$nodes = array(), $column = 'ct', $direction = "asc") {
    self::$sortDirection = $direction;
    self::$sortColumn = $column;
    usort($nodes, "XHProfGraph::compareNodes");
  }

  public static function compareNodes($a, $b) {
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

class XHProfNode {

  public $fn = '';
  public $ct = 0;
  public $wt = 0;
  public $cpu = 0;
  public $mu = 0;
  public $pmu = 0;

  public $calculated_data = array();

  public function __construct($fn, $data) {
    $this->fn = $fn;
    foreach ($data as $key => $value) {
      $this->$key = $value;    
    }
  }

  public function asArray() {
    global $xhprof_data_fields;
    $array = array();    
    foreach ($xhprof_data_fields as $field => $name) {
      $array[$field] = $this->$field;
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


