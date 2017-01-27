<?php

namespace Netpath\Model;

use Netpath\Interfaces\INetDevice;

class NetDevice extends Device implements INetDevice
{
  private $visited = false;
  private $latency = INF;
  private $upstream;

  public function setLatency(int $latency) {
    $this->latency = $latency;
  }

  public function getLatency() {
    return $this->latency;
  }

  public function setUpstream(INetDevice $device) {
    $this->upstream = $device;
  }
  
  public function getUpstream() {
    return $this->upstream;
  }
  
  public function isVisited() {
    return $this->visited;
  }
  
  public function setVisited() {
    $this->visited = true;
  }  
}