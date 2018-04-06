#!/usr/bin/env php
<?php

require_once __DIR__ . '/init.php';

use AnrDaemon\Math\Point;
use AnrDaemon\Minecraft\NBT;

$nbt = new NBT\TAG_Compound('Schematic');
$nbt[] = $map = new NBT\TAG_Compound('SchematicaMapping');
$nbt[] = new NBT\TAG_Short('Width', $dims->x);
$nbt[] = new NBT\TAG_Short('Length', $dims->y);
$nbt[] = new NBT\TAG_Short('Height', $dims->z);
$nbt[] = new NBT\TAG_String('Materials', 'Alpha');
$nbt[] = $blocks = new NBT\TAG_Byte_Array('Blocks', array_fill(0, $dims->x * $dims->y * $dims->z, 0));
$nbt[] = $data = new NBT\TAG_Byte_Array('Data', array_fill(0, $dims->x * $dims->y * $dims->z, 0));
$nbt[] = new NBT\TAG_List('Entities');
$nbt[] = new NBT\TAG_List('TileEntities');

$map[] = new NBT\TAG_Short('minecraft:air', 0);
$map[] = new NBT\TAG_Short('minecraft:cobblestone', 1);
$map[] = new NBT\TAG_Short('minecraft:quartz_block', 2);

foreach($trunks as $trunk)
{
  $trunk->len = $trunk->p0->distance($trunk->p1);
  for($z = $trunk->red->z; $z <= $trunk->blue->z; $z++)
  {
    for($y = $trunk->red->y; $y <= $trunk->blue->y; $y++)
    {
      for($x = $trunk->red->x; $x <= $trunk->blue->x; $x++)
      {
        if($trunk->toVoxel($x, $y, $z))
          $blocks[$x - $red->x + $dims->x * ($y - $red->y + $dims->y * ($z - $red->z))] = 1;
      }
    }
  }
}

$blocks[$white->x - $red->x + $dims->x * ($white->y - $red->y + $dims->y * ($white->z - $red->z))] = 2;

$white = $red->translate($offset);
$fname = "branch_{$_GET['seed']}({$white->x},{$white->z},{$white->y}).schematic";

if(PHP_SAPI === 'cli')
{
  $file = new NBT\CompressedWriter(new \SplFileObject(__DIR__ . "/$fname", 'wb'));
}
else
{
  header('Content-Type: application/x-minecraft-schematic');
  header(sprintf('Content-Disposition: attachment; filename="%s"', urlencode($fname)));
  $file = new NBT\CompressedWriter(new \SplFileObject("php://stdout", 'ab'));
}

$file->write($nbt);
unset($file);
