<?php

namespace Netpath\Interfaces;

interface INetDevice
{
  public function setLatency(int $latency);
  public function getLatency();
  public function setUpstream(INetDevice $device);
  public function getUpstream();
  public function isVisited();
  public function setVisited();
}