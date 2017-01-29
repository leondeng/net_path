<?php

namespace Netpath\Model;

use Netpath\Interfaces\INode;
use Netpath\Interfaces\IDevice;

class Node implements INode
{
  private $device;

  private $visited = false;
  private $latency = INF; // store total laterncy from source node of path, initialized as INF
  private $previous; // store previous node on the path

  public function __construct(IDevice $device) {
    $this->device = $device;
  }

  public function getDevice() {
    return $this->device;
  }

  public function setLatency(int $latency) {
    $this->latency = $latency;
  }

  public function getLatency() {
    return $this->latency;
  }

  public function setPreviousNode(INode $node) {
    $this->previous = $node;
  }
  
  public function getPreviousNode() {
    return $this->previous;
  }
  
  public function isVisited() {
    return $this->visited;
  }
  
  public function setVisited() {
    $this->visited = true;
  }

  /**
   * Report path plus latency
   *
   * @param  $reversed: report path in reversed direction
   * @return string
   */
  public function report(bool $reversed = false) {
    $node = $this;
    $path = [];
    $method = $reversed ? 'array_push' : 'array_unshift';

    do {
      $method($path, $node);
      $node = $node->getPreviousNode();
    } while ($node);

    array_push($path, $this->getLatency());

    return implode('=>', $path);
  }

  public function __toString() {
    return (string) $this->device;
  }
}