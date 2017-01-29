<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Runner\Engine;
use Netpath\Model\ConnCollection;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class EngineTest extends TestCase
{
  const NO_PATH_FIXTURES = [
    [
      'from' => 'A',
      'to'  => 'B',
      'latency' => 10,
    ],
    [
      'from' => 'A',
      'to'  => 'C',
      'latency' => 20,
    ],
    [
      'from' => 'B',
      'to'  => 'D',
      'latency' => 100,
    ],
    [
      'from' => 'C',
      'to'  => 'D',
      'latency' => 30,
    ],
    [
      'from' => 'E',
      'to'  => 'F',
      'latency' => 1000,
    ],
  ];

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
  public function test_device_not_found_exception() {
    $engine = $this->getEngine();
    $engine->findPath('X', 'Y', 1024);
  }

  /**
   * @expectedException \Netpath\Exception\InSituException
   */
  public function test_in_situ_exception() {
    $engine = $this->getEngine();
    $engine->findPath('A', 'A', 1024);
  }

  /**
   * @expectedException \Netpath\Exception\PathNotFoundException
   */
  public function test_path_not_found_exception_latency() {
    $engine = $this->getEngine();
    $engine->findPath('E', 'F', 999);
  }

  /**
   * @expectedException \Netpath\Exception\PathNotFoundException
   */
  public function test_path_not_found_exception_no_path() {
    parent::$no_path_check = true;
    $engine = $this->getEngine();
    parent::$no_path_check = false;

    $engine->findPath('A', 'F', 1100);
  }

  public function test_find_path() {
    $engine = $this->getEngine();

    foreach (parent::MINLATENCY as $from => $tos) {
      foreach ($tos as $to => $min_latency) {
        $max_latency = $min_latency + 10;
        // $this->log("Testing: from $from to $to in max $max_latency...\n");

        $target_node = $engine->findPath($from, $to, $max_latency);

        $this->assertTrue($target_node->isVisited());
        $this->assertEquals($min_latency, $target_node->getLatency());
      }
    }
  }

  private function getEngine() {
    if (!$this->engine) {
      $this->engine = new Engine($this->getConnCollection());
    }

    return $this->engine;
  }
}
