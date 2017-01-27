<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Runner\Engine;
use Netpath\Model\ConnCollection;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class EngineTest extends TestCase
{
  private $engine;

  public function test_init() {
    $engine = $this->getEngine();

    $this->assertEquals(6, count($engine->getDevices()));
    $this->assertEquals('A,B,C,D,E,F', implode(',', array_keys($engine->getDevices())));
    $this->assertEquals('A,B,C,D,E,F', implode(',', array_map(function($device) {
      return (string) $device;
    }, $engine->getDevices())));
  }

  /**
   * @expectedException \Netpath\Exception\DeviceNotFoundException
   */
  public function test_find_path_exception() {
    $engine = $this->getEngine();
    $engine->findPath('X', 'Y', 1024);
  }

  private function getEngine() {
    if (!$this->engine) {
      $this->engine = new Engine($this->getConnCollection());
    }

    return $this->engine;
  }
}
