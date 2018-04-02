#!/usr/bin/env php -f
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use AnrDaemon\Minecraft\NBT;
use AnrDaemon\Misc\Coordinate3D as Coord;

$fileName = __DIR__ . '/Schematica.schematic';

// Anchor point at the center of back (Y(z)=0) plane.
$white = new Coord(1, 0, 1);

$test = array();
// Two blocks on the bottom corners of a back plane. (Y(z)=Z(y)=0)
$test[] = new Coord(0, 0, 0);
$test[] = new Coord(2, 0, 0);
// Two blocks on furthest vetical edge.
$test[] = new Coord(2, 2, 0);
$test[] = new Coord(2, 2, 2);

$nbt = new NBT\TAG_Compound('Schematic');
$nbt[] = new NBT\TAG_String('Materials', 'Alpha');
$nbt[] = new NBT\TAG_Short('Width', $width = 3);
$nbt[] = new NBT\TAG_Short('Length', $length = 3);
$nbt[] = new NBT\TAG_Short('Height', $height = 3);
$nbt[] = $map = new NBT\TAG_Compound('SchematicaMapping');
$nbt[] = $data = new NBT\TAG_Byte_Array('Data', array_fill(0, $width * $length * $height, 0));
$nbt[] = $blocks = new NBT\TAG_Byte_Array('Blocks', array_fill(0, $width * $length * $height, 0));

$map[] = new NBT\TAG_Short('minecraft:air', 0);
$map[] = new NBT\TAG_Short('minecraft:cobblestone', 1);
$map[] = new NBT\TAG_Short('minecraft:quartz_block', 2);

foreach($test as $point)
{
  $blocks[$point['x'] + $width * ($point['y'] + $length * $point['z'])] = 1;
}

$blocks[$white['x'] + $width * ($white['y'] + $length * $white['z'])] = 2;

$nbt[] = new NBT\TAG_List('Entities');
$nbt[] = new NBT\TAG_List('TileEntities');

var_dump($nbt);

$file = new NBT\CompressedWriter(new \SplFileObject($fileName, 'wb'));
$file->write($nbt);
