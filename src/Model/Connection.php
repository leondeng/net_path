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

  public function __toString() {
    return sprintf('%s %s %d', $this->from, $this->to, $this->latency);
  }
}