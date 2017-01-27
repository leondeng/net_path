<?php

namespace Netpath\Model;

use Netpath\Interfaces\IConnCollection;
use Netpath\Interfaces\IConnection;
use Netpath\Interfaces\INetDevice;

class ConnCollection implements IConnCollection
{
  private $connections = [];

  public function addConnection(IConnection $connection) {
    $this->connections[] = $connection;
  }

  public function getConnections() {
    return $this->connections;
  }

  public function getLatencyBetween(INetDevice $from, INetDevice $to) {

  }

  public function findOneDownstreamFor(INetDevice $device) {

  }
}