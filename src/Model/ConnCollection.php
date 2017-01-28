<?php

namespace Netpath\Model;

use Netpath\Interfaces\IConnCollection;
use Netpath\Interfaces\IConnection;
use Netpath\Interfaces\IDevice;
use Netpath\Exception\ConnectionNotFoundException;

class ConnCollection implements IConnCollection
{
  private $connections = [];

  public function addConnection(IConnection $connection) {
    $this->connections[] = $connection;
  }

  public function getConnections() {
    return $this->connections;
  }

  public function getLatencyBetween(IDevice $from, IDevice $to) {
    $from_name = $from->getName();
    $to_name = $to->getName();

    if ($from_name > $to_name) {
      $from_name = $to->getName();
      $to_name = $from->getName();
    }

    $connections = array_filter($this->connections, function($conn) use($from_name, $to_name) {
      return $conn->getFromDevice()->getName() === $from_name &&
        $conn->getToDevice()->getName() === $to_name;
    });

    if (empty($connections)) {
      throw new ConnectionNotFoundException("No connection between $from and $to.\n");
    }

    return array_values($connections)[0]->getLatency();
  }

  public function findLinkedDevicesFor(IDevice $device) {
    return array_filter(array_map(function($conn) use($device) {
      if ($conn->getFromDevice()->getName() === $device->getName()) {
        return $conn->getToDevice();
      }

      if ($conn->getToDevice()->getName() === $device->getName()) {
        return $conn->getFromDevice();
      }

      return false;

    }, $this->connections));
  }
}