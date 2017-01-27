<?php

namespace Netpath\Model;

use Netpath\Interfaces\IDevice;

class Device implements IDevice
{
  private $name;

  public function __construct(string $name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function __toString() {
    return $this->name;
  }
}