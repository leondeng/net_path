<?php

namespace Netpath\Model;

use Netpath\Interfaces\IConnection;
use Netpath\Interfaces\IDevice;

class Connection implements IConnection
{
  private $from;
  private $to;
  private $latency;

  public function __construct(IDevice $from, IDevice $to, int $latency) {
    $this->from = $from;
    $this->to = $to;
    $this->latency = $latency;
  }

  public function getFromDevice() {
    return $this->from;
  }

  public function getToDevice() {
    return $this->to;
  }

  public function getLatency() {
    return $this->latency;
  }

  public function __toString() {
    return sprintf('%s %s %d', $this->from, $this->to, $this->latency);
  }
}