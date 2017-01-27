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


}