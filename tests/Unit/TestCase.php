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

  const DOWNSTREAMS = [
    'A' => 'B,C',
    'B' => 'D',
    'C' => 'D',
    'D' => 'E',
    'E' => 'F',
    'F' => '',
  ];

  protected $devices = [];
  protected $connections = [];
  protected $collection;

  protected function loadFixtures() {
    foreach (self::FIXTURES as $fixture) {
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
}