<?php

class XHProfGraph {

  public $nodes = array();
  public $edges = array();

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

  public function findConnections($method = "main()") {
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
