<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\Device;

class DeviceTest extends TestCase
{
  public function test_init() {
    $name = 'Device_Name';
    $device = new Device($name);
    $this->assertEquals($name, (string) $device);
  }
}