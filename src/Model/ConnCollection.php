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

  /**
   * Get latency between two devices
   * 
   * @param  IDevice $from
   * @param  IDevice $to
   * @return int
   */
  public function getLatencyBetween(IDevice $from, IDevice $to) {
    $from_name = "$from";
    $to_name = "$to";

    if ($from_name > $to_name) {
      $from_name = "$to";
      $to_name = "$from";
    }

    $connections = array_filter($this->connections, function($conn) use($from_name, $to_name) {
      return (string) $conn->getFromDevice() === $from_name &&
        (string) $conn->getToDevice() === $to_name;
    });

    if (empty($connections)) {
      throw new ConnectionNotFoundException("No connection between $from and $to.\n");
    }

    return array_values($connections)[0]->getLatency();
  }

  /**
   * Find directly linked devices for given device
   * 
   * @param  IDevice $device
   * @return array
   */
  public function findLinkedDevicesFor(IDevice $device) {
    return array_filter(array_map(function($conn) use($device) {
      if ((string) $conn->getFromDevice() === "$device") {
        return $conn->getToDevice();
      }

      if ((string) $conn->getToDevice() === "$device") {
        return $conn->getFromDevice();
      }

      return false;

    }, $this->connections));
  }
}