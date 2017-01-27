<?php

namespace Netpath\Runner;

use Netpath\Interfaces\IConnCollection;
use Netpath\Interfaces\IConnection;
use Netpath\Interfaces\INetDevice;
use Netpath\Interfaces\IDevice;
use Netpath\Interfaces\IEngine;
use Netpath\Exception\DeviceNotFoundException;

class Engine implements IEngine
{
  private $devices = [];
  private $collection;

  public function __construct(IConnCollection $collection) {
    $this->collection = $collection;
    $this->loadDevices();
  }

  private function loadDevices() {
    foreach ($this->collection->getConnections() as $conn) {
      $this->addDevice($conn->getFromDevice());
      $this->addDevice($conn->getToDevice());
    }

    ksort($this->devices);
  }

  private function addDevice(IDevice $device) {
    if (!array_key_exists($device->getName(), $this->devices)) {
      $this->devices[$device->getName()] = $device;
    }
  }

  public function getDevices() {
    return $this->devices;
  }

  public function findPath(string $source, string $target, int $max_latency) {
    if (!(array_key_exists($source, $this->devices) &&
       array_key_exists($target, $this->devices))) {
      throw new DeviceNotFoundException;
    }

    //TODO
  }

  public function reset() {

  }
}