<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\ConnCollection;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class ConnCollectionTest extends TestCase
{
  private $devices = [];
  private $connections = [];
  private $collection;

  public function test_add_connection() {
    $collection = new ConnCollection;

    $collection->addConnection($this->getConnection());
    $this->assertEquals(1, count($collection->getConnections()));
  }

  public function test_get_latency_between() {
    $collection = $this->getConnCollection();

    foreach (parent::FIXTURES as $fixture) {
      $latency = $collection->getLatencyBetween($this->getDevice($fixture['from']), $this->getDevice($fixture['to']));
      $this->assertEquals($fixture['latency'], $latency);
    }
  }

  public function test_find_downstream_for() {
    $collection = $this->getConnCollection();

    foreach ($this->getDevices() as $name => $device) {
      $downstreams = $collection->findDownstreamsFor($device);
      $this->assertEquals(parent::DOWNSTREAMS[$name], implode(',', array_map(function($device) {
        return (string) $device;
      }, $downstreams)));
    }
  }

  private function loadFixtures() {
    foreach (parent::FIXTURES as $fixture) {
      $from = $this->getDevice($fixture['from']);
      $to = $this->getDevice($fixture['to']);
      $conn = new Connection($from, $to, $fixture['latency']);

      $this->connections[] = $conn;
    }
  }

  private function getDevice(string $name) {
    if (array_key_exists($name, $this->devices)) {
      return $this->devices[$name];
    }

    return $this->devices[$name] = new Device($name);
  }

  private function getDevices() {
    if (empty($this->devices)) {
      $this->loadFixtures();
    }

    return $this->devices;
  }

  private function getConnection(int $index = 0) {
    if (empty($this->connections)) {
      $this->loadFixtures();
    }

    return $this->connections[$index];
  }

  private function getConnections() {
    if (empty($this->connections)) {
      $this->loadFixtures();
    }

    return $this->connections;
  }

  private function getConnCollection() {
    if ($this->collection) {
      return $this->collection;
    }

    $this->collection = new ConnCollection;

    foreach ($this->getConnections() as $conn) {
      $this->collection->addConnection($conn);
    }

    return $this->collection;
  }
}