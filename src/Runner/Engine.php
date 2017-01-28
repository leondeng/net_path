<?php

namespace Netpath\Runner;

use Netpath\Interfaces\IConnCollection;
use Netpath\Interfaces\IConnection;
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

      foreach ($this->collection->findLinkedDevicesFor($node) as $device) {
        if ($this->nodes["$device"]->isVisited()) {
          continue;
        }

        $latency = $node->getLatency() + $this->collection->getLatencyBetween($node, $device);

        $downstream = $this->nodes["$device"];

        if ($latency < $downstream->getLatency()) {
          $downstream->setLatency($latency);
          $downstream->setUpstream($node);
        }
      }
    } while (!$this->isDone());

    return $this->getResult();
  }

  /**
   * Get path searching result
   * 
   * @return INetDevice
   * @throws PathNotFoundException
   */
  private function getResult() {
    $target_node = $this->getTargetNode();

    if (!$target_node->isVisited() || $target_node->getLatency() > $this->max_latency) {
      throw new PathNotFoundException;
    }

    return $target_node;
  }

  /**
   * 2 conditions of finish: target visited, or target unreachable
   * 
   * @return boolean
   */
  public function isDone() {
    return $this->getTargetNode()->isVisited() || !$this->getMinLatencyNode();
  }

  /**
   * Generate report
   * 
   * @return string
   */
  public function report() {
    $node = $this->getTargetNode();
    $path = [];

    do {
      $this->isReversed ? array_push($path, $node) : array_unshift($path, $node);
      $node = $node->getUpstream();
    } while ($node);

    array_push($path, $this->getTargetNode()->getLatency());

    return implode('=>', $path);
  }

  private function getTargetNode() {
    return $this->nodes[$this->target];
  }

  /**
   * Get unvisited node with min latency
   * 
   * @return INetDevice
   */
  private function getMinLatencyNode() {
    return array_reduce($this->nodes, function($carry, $node) {
      if (!$node->isVisited() && (!$carry || ($node->getLatency() < $carry->getLatency()))) {
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

    $this->reset();

    $this->isReversed = $source > $target;
    $this->source = $this->isReversed ? $target : $source;
    $this->target = $this->isReversed ? $source : $target;
    $this->max_latency = $max_latency;

    $this->nodes = $this->rebuildNodes();
    $this->nodes[$this->source]->setLatency(0);
  }

  private function reset() {
    $this->nodes = [];
    $this->source = $this->target = $this->max_latency = null;
    $this->isReversed = false;
  }
}