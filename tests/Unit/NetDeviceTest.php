<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\NetDevice;

class NetDeviceTest extends TestCase
{
  public function test_init() {
    $name = 'NetDevice_Name';
    $device = new NetDevice($name);
    
    $this->assertEquals($name, (string) $device);
    $this->assertFalse($device->isVisited());
    $this->assertEquals(INF, $device->getLatency());
    $this->assertNull($device->getUpstream());
  }
}