<?php

namespace Netpath\Interfaces;

interface IConnection
{
  public function getFromDevice();
  public function getToDevice();
  public function getLatency();
}