<?php

namespace Netpath\Tests\Unit;

use PHPUnit_Framework_TestCase;
use Netpath\Model\ConnCollection;
use Netpath\Model\Connection;
use Netpath\Model\Device;

class TestCase extends PHPUnit_Framework_TestCase
{
  const FIXTURES = [
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
      'from' => 'D',
      'to'  => 'E',
      'latency' => 10,
    ],
    [
      'from' => 'E',
      'to'  => 'F',
      'latency' => 1000,
    ],
  ];

  const LINKED_DEVICES = [
    'A' => 'B,C',
    'B' => 'A,D',
    'C' => 'A,D',
    'D' => 'B,C,E',
    'E' => 'D,F',
    'F' => 'E',
  ];

  const MINLATENCY = [
    'A' => [
      'B' => 10,
      'C' => 20,
      'D' => 50,
      'E' => 60,
      'F' => 1060,
    ],
    'B' => [
      'A' => 10,
      'C' => 30,
      'D' => 60,
      'E' => 70,
      'F' => 1070,
    ],
    'C' => [
      'A' => 20,
      'B' => 30,
      'D' => 30,
      'E' => 40,
      'F' => 1040,
    ],
    'D' => [
      'A' => 50,
      'B' => 60,
      'C' => 30,
      'E' => 10,
      'F' => 1010,
    ],
    'E' => [
      'A' => 60,
      'B' => 70,
      'C' => 40,
      'D' => 10,
      'F' => 1000,
    ],
    'F' => [
      'A' => 1060,
      'B' => 1070,
      'C' => 1040,
      'D' => 1010,
      'E' => 1000,
    ],
  ];

  protected $devices = [];
  protected $connections = [];
  protected $collection;

  public static $no_path_check = false;

  protected function loadFixtures() {
    $fixtures = self::$no_path_check ? static::NO_PATH_FIXTURES : self::FIXTURES;

    foreach ($fixtures as $fixture) {
      $from = $this->getDevice($fixture['from']);
      $to = $this->getDevice($fixture['to']);
      $conn = new Connection($from, $to, $fixture['latency']);

      $this->connections[] = $conn;
    }
  }

  protected function getDevice(string $name) {
    if (array_key_exists($name, $this->devices)) {
      return $this->devices[$name];
    }

    return $this->devices[$name] = new Device($name);
  }

  protected function getDevices() {
    if (empty($this->devices)) {
      $this->loadFixtures();
    }

    return $this->devices;
  }

  protected function getConnection(int $index = 0) {
    if (empty($this->connections)) {
      $this->loadFixtures();
    }

    return $this->connections[$index];
  }

  protected function getConnections() {
    if (empty($this->connections)) {
      $this->loadFixtures();
    }

    return $this->connections;
  }

  protected function getConnCollection() {
    if ($this->collection) {
      return $this->collection;
    }

    $this->collection = new ConnCollection;

    foreach ($this->getConnections() as $conn) {
      $this->collection->addConnection($conn);
    }

    return $this->collection;
  }

  protected function log(string $message) {
    echo $message;
  }
}