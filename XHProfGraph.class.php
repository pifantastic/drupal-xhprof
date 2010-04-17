<?php

class XHProfGraph {

  public $nodes = array();
  public $edges = array();
  
  public static $sortDirection = "asc";
  public static $sortColumn = "ct";

  public function __construct($run) {
    foreach ($run->data as $call => $data) {
      list($parent_method, $child_method) = explode('==>', $call);
      
      $parent_node = new XHProfNode($parent_method, $data);
      $this->addNode($parent_node);
      
      if ($child_method) {
        $child_node = new XHProfNode($child_method, $data);
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
      if ($node->method == $n->method) {
        return $n;
      }
    }
    
    return FALSE;
  }

  public function findEdge($edge) {
    foreach ($this->edges as $e) {
      if ($e->node1->method == $edge->node1->method and $e->node2->method == $edge->node2->method) {
        return $e;
      }
    }
    
    return FALSE;
  }

  public function findChildren($method = "main()") {
    $connections = array();
    foreach ($this->edges as $edge) {
      if ($edge->node1->method == $method) {
        $connections[] = $edge->node2;
      }
    }
    return $connections;
  }
  
  public function findParents($method = "main()") {
    $parents = array();    
    foreach ($this->edges as $edge) {
      if ($edge->node2->method == $method) {
        $parents[] = $edge->node1;
      }
    }
    return $parents;
  }

  public function nodeForMethod($method) {
    foreach ($this->nodes as $node) {
      if ($node->method == $method) {
        return $node;
      }
    }

    return FALSE;
  }
  
  public static function sortNodes($nodes = array(), $column, $direction = "asc") {
    self::$sortDirection = $direction;
    self::$sortColumn = $columm;
    $nodes = (array) $nodes;
    usort($nodes, "XHProfGraph::sortNodes");
  }

  public static function compareNodes($a, $b) {
    if (!isset($a->data[self::$sortColumn])) {
      return 0;
    }
    
    if ($a->data[self::$sortColumn] == $b->data[self::$sortColumn]) {
      return 0;
    }

    if (self::$sortDirection == "asc") {
      return ($a->data[self::$sortColumn] > $b->data[self::$sortColumn]) ? 1 : -1;
    } else {
      return ($a->data[self::$sortColumn] > $b->data[self::$sortColumn]) ? -1 : 1;
    }
  }

}

class XHProfNode {
  
  public $method = array();
  public $data = array();

  public function __construct($method, $data) {
    $this->method = $method;
    $this->data = $data;
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


