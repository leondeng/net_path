<?php

namespace Netpath\Interfaces;

interface INode
{
  public function getDevice();
  public function setLatency(int $latency);
  public function getLatency();
  public function setPreviousNode(INode $node);
  public function getPreviousNode();
  public function isVisited();
  public function setVisited();
  public function report();
}