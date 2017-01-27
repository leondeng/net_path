<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class ConnectionTest extends TestCase
{
  public function test_init() {
    $devices = $this->getDevices();

    $conn = new Connection($devices[0], $devices[1], 100);
    $this->assertEquals('from to 100', (string) $conn);
  }

  private function getDevices() {
    return [
      new Device('from'),
      new Device('to')
    ];
  }
}