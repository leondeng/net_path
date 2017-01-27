<?php

namespace Netpath\Interfaces;

interface IConnCollection
{
  public function addConnection(IConnection $connection);
  public function getLatencyBetween(IDevice $from, IDevice $to);
  public function findDownstreamsFor(IDevice $device);
}