#!/usr/bin/php7 -f
<?php

if(version_compare(PHP_VERSION, '7.0', '<'))
  throw new Exception('Go upgrade, k?');

require_once dirname(__DIR__) . '/vendor/autoload.php';

use AnrDaemon\Minecraft\NBT;

$file = new NBT\Reader(new \SplFileObject('compress.zlib://' . __DIR__ . '/level.dat', 'rb'));
$nbt = $file->read();
unset($file);

var_dump($nbt);

$file = new NBT\Writer(new \SplFileObject(__DIR__ . '/level.flat', 'wb'));
$file->write($nbt);
unset($file);
