<?php

namespace Netpath\Tests\Unit;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
  const FIXTURES = [
    [
      'from' => 'A',
      'to'  => 'B',
      'latency' => 10,
    ],
    [
      'from' => 'A',
      'to'  => 'C',
      'latency' => 20,
    ],
    [
      'from' => 'B',
      'to'  => 'D',
      'latency' => 100,
    ],
    [
      'from' => 'C',
      'to'  => 'D',
      'latency' => 30,
    ],
    [
      'from' => 'D',
      'to'  => 'E',
      'latency' => 10,
    ],
    [
      'from' => 'E',
      'to'  => 'F',
      'latency' => 1000,
    ],
  ];

  const DOWNSTREAMS = [
    'A' => 'B,C',
    'B' => 'D',
    'C' => 'D',
    'D' => 'E',
    'E' => 'F',
    'F' => '',
  ];
}