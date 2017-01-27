<?php

namespace Netpath\Interfaces;

interface IConnCollection
{
  public function addConnection(IConnection $connection);
  public function getLatencyBetween(INetDevice $from, INetDevice $to);
  public function findOneDownstreamFor(INetDevice $device);
}