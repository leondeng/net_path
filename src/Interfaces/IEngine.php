<?php

namespace Netpath\Interfaces;

interface IEngine
{
  public function findPath(string $source, string $target, int $max_latency);
  public function reset();
  public function getDevices();
}