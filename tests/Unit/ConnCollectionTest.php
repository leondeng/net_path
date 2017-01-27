<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\ConnCollection;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class ConnCollectionTest extends TestCase
{
  public function test_add_connection() {
    $collection = new ConnCollection;

    $collection->addConnection($this->getConnection());
    $this->assertEquals(1, count($collection->getConnections()));
  }

  private function getConnection() {
    $devices = $this->getDevices();

    return new Connection($devices[0], $devices[1], 100);
  }
  private function getDevices() {
    return [
      new Device('from'),
      new Device('to')
    ];
  }
}