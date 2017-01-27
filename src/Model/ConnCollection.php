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
    $connections = array_filter($this->connections, function($conn) use($from, $to) {
      return $conn->getFromDevice()->getName() === $from->getName() &&
        $conn->getToDevice()->getName() === $to->getName();
    });

    if (empty($connections)) {
      throw new ConnectionNotFoundException;
    }

    return array_values($connections)[0]->getLatency();
  }

  public function findDownstreamsFor(IDevice $device) {
    $connections = array_filter($this->connections, function($conn) use ($device) {
      return $conn->getFromDevice()->getName() === $device->getName();
    });

    return array_map(function($conn) {
      return $conn->getToDevice();
    }, $connections);
  }
}