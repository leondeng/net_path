<?php

namespace Netpath\Runner;

use Netpath\Interfaces\IConnCollection;
use Netpath\Interfaces\INetDevice;
use Netpath\Interfaces\IDevice;
use Netpath\Interfaces\IEngine;
use Netpath\Model\NetDevice;
use Netpath\Exception\DeviceNotFoundException;
use Netpath\Exception\PathNotFoundException;
use Netpath\Exception\InSituException;

class Engine implements IEngine
{
  private $devices = [];
  private $collection;
  private $nodes = [];

  private $source;
  private $target;
  private $max_latency;
  private $isReversed = false;
  private $target_node;

  public function __construct(IConnCollection $collection) {
    $this->collection = $collection;
    $this->loadDevices();
  }

  private function loadDevices() {
    foreach ($this->collection->getConnections() as $conn) {
      $this->addDevice($conn->getFromDevice());
      $this->addDevice($conn->getToDevice());
    }

    ksort($this->devices);
  }

  private function addDevice(IDevice $device) {
    if (!array_key_exists("$device", $this->devices)) {
      $this->devices["$device"] = $device;
    }
  }

  public function getDevices() {
    return $this->devices;
  }

  /**
   * Find a path from source to target
   * 
   * @param  string $source
   * @param  string $target
   * @param  int    $max_latency
   * @return INetDevice
   */
  public function findPath(string $source, string $target, int $max_latency) {
    $this->init($source, $target, $max_latency);

    do {
      $node = $this->getMinLatencyNode();
      $node->setVisited();
      unset($this->nodes["$node"]);

      foreach ($this->findNeighborsFor($node) as $neighbor) {
        $latency = $node->getLatency() + $this->collection->getLatencyBetween($node, $neighbor);

        if ($latency < $neighbor->getLatency()) {
          $neighbor->setLatency($latency);
          $neighbor->setUpstream($node);
        }
      }
    } while (!$this->isDone());

    return $this->getResult();
  }

  /**
   * Find unvisited neighbor nodes for given nodes
   * 
   * @param  INetDevice $node
   * @return array
   */
  private function findNeighborsFor(INetDevice $node) {
    $devices = $this->collection->findLinkedDevicesFor("$node");

    return array_filter($this->nodes, function($node) use($devices) {
      if (in_array("$node", $devices)) {
        return $node;
      }

      return false;
    });
  }

  /**
   * Get path searching result
   * 
   * @return INetDevice
   * @throws PathNotFoundException
   */
  private function getResult() {
    if (!$this->target_node->isVisited() ||
        $this->target_node->getLatency() > $this->max_latency) {
      throw new PathNotFoundException;
    }

    return $this->target_node;
  }

  /**
   * 2 conditions of finish: target visited, or target unreachable
   * 
   * @return boolean
   */
  public function isDone() {
    return $this->target_node->isVisited() || !$this->getMinLatencyNode();
  }

  /**
   * Generate report
   * 
   * @return string
   */
  public function report() {
    $node = $this->target_node;
    $path = [];

    do {
      $this->isReversed ? array_push($path, $node) : array_unshift($path, $node);
      $node = $node->getUpstream();
    } while ($node);

    array_push($path, $this->target_node->getLatency());

    return implode('=>', $path);
  }

  /**
   * Get unvisited node with min latency
   * 
   * @return INetDevice
   */
  private function getMinLatencyNode() {
    return array_reduce($this->nodes, function($carry, $node) {
      if (!$carry || ($node->getLatency() < $carry->getLatency())) {
        $carry = $node;
      }
      return $carry;
    }, false);
  }

  private function rebuildNodes() {
    return array_combine(array_keys($this->devices), array_map(function(IDevice $device) {
      return new NetDevice("$device");
    }, $this->devices));
  }

  private function init(string $source, string $target, int $max_latency) {
    $diff = array_diff([$source, $target], array_keys($this->devices));
    if (!empty($diff)) {
      throw new DeviceNotFoundException(sprintf("Device %s not existing.\n", implode(' and ', $diff)));
    }

    if ($source == $target) {
      throw new InSituException;
    }

    $this->isReversed = $source > $target;
    $this->source = $this->isReversed ? $target : $source;
    $this->target = $this->isReversed ? $source : $target;
    $this->max_latency = $max_latency;

    $this->nodes = $this->rebuildNodes();
    $this->nodes[$this->source]->setLatency(0);
    $this->target_node = $this->nodes[$this->target];
  }
}
