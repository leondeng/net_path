<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Netpath\Model\Device;
use Netpath\Model\Connection;
use Netpath\Model\ConnCollection;
use Netpath\Runner\Engine;
use Netpath\Exception\ConnectionNotFoundException;
use Netpath\Exception\DeviceNotFoundException;
use Netpath\Exception\PathNotFoundException;
use Netpath\Exception\InSituException;

echo "Setting up network...";

$lines = array_map(function($line) {
  return rtrim($line);
}, file(__DIR__ . '/input.csv'));

$collection = new ConnCollection;

foreach ($lines as $line) {
  $input = explode(',', $line);
  $from = new Device($input[0]);
  $to = new Device($input[1]);

  $connection = new Connection($from, $to, (int) $input[2]);
  $collection->addConnection($connection);
}

$engine = new Engine($collection);

echo "Done.\n";

echo "Input [Device From][Device To][Time] to find path, 'QUIT' to exit.\n";

do {
  echo "Input: ";
  $handle = fopen("php://stdin", "r");
  $line = fgets($handle);

  if (trim($line) == 'QUIT') {
      echo "Bye!\n";
      exit;
  }

  $params = explode(' ', trim($line));

  try {
    echo "Output: " . $engine
      ->findPath($params[0], $params[1], (int) $params[2])
      ->report($engine->getIsReversed()) . PHP_EOL
    ;
  } catch (PathNotFoundException $e) {
    echo "Path not found.\n";
  } catch (InSituException $e) {
    echo "[Device To] can not be equal to [Device From].\n";
  } catch (ConnectionNotFoundException $e) {
    echo $e->getMessage();
  } catch (DeviceNotFoundException $e) {
    echo $e->getMessage();
  } catch (\Exception $e) {
    throw $e;
  }
} while (true);
