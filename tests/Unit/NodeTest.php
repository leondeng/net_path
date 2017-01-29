<?php

namespace Netpath\Tests\Unit;

use Netpath\Tests\Unit\TestCase;
use Netpath\Model\Device;
use Netpath\Model\Node;

class NodeTest extends TestCase
{
  public function test_init() {
    $name = 'NetDevice_Name';
    $device = new Device($name);
    $node = new Node($device);
    
    $this->assertEquals($name, (string) $node);
    $this->assertEquals($name, (string) $node->getDevice());
    $this->assertFalse($node->isVisited());
    $this->assertEquals(INF, $node->getLatency());
    $this->assertNull($node->getPreviousNode());
  }

  public function test_mutators() {
    $node = new Node($this->getDevice('A'));
    $previous = new Node($this->getDevice('B'));

    $node->setVisited();
    $node->setLatency(100);
    $node->setPreviousNode($previous);

    $this->assertTrue($node->isVisited());
    $this->assertEquals(100, $node->getLatency());
    $this->assertInstanceOf(Node::class, $node->getPreviousNode());
    $this->assertEquals('B', (string) $node->getPreviousNode());
  }

  public function test_report() {
    $node = new Node($this->getDevice('A'));
    $pnode = new Node($this->getDevice('B'));
    $ppnode = new Node($this->getDevice('C'));

    $node->setLatency(100);
    $pnode->setPreviousNode($ppnode);
    $node->setPreviousNode($pnode);

    $this->assertEquals('C=>B=>A=>100', $node->report());
    $this->assertEquals('A=>B=>C=>100', $node->report(true)); //reverse check
  }
}