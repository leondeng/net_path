<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\ConnCollection;

class ConnCollectionTest extends TestCase
{
  public function test_add_connection() {
    $collection = new ConnCollection;

    $collection->addConnection($this->getConnection());
    $this->assertEquals(1, count($collection->getConnections()));
  }

  /**
   * @expectedException \Netpath\Exception\ConnectionNotFoundException
   */
  public function test_connection_not_found_exception() {
    $collection = $this->getConnCollection();
    $collection->getLatencyBetween($this->getDevice('A'), $this->getDevice('D'));
  }

  public function test_get_latency_between() {
    $collection = $this->getConnCollection();

    foreach (parent::FIXTURES as $fixture) {
      $latency = $collection->getLatencyBetween($this->getDevice($fixture['from']), $this->getDevice($fixture['to']));
      $this->assertEquals($fixture['latency'], $latency);
      //reverse check
      $reverse_latency = $collection->getLatencyBetween($this->getDevice($fixture['to']), $this->getDevice($fixture['from']));
      $this->assertEquals($fixture['latency'], $reverse_latency);
    }
  }

  public function test_find_linked_devices_for() {
    $collection = $this->getConnCollection();

    foreach ($this->getDevices() as $name => $device) {
      $downstreams = $collection->findLinkedDevicesFor($device);
      $this->assertEquals(parent::LINKED_DEVICES[$name], implode(',', array_map(function($device) {
        return (string) $device;
      }, $downstreams)));
    }
  }


}