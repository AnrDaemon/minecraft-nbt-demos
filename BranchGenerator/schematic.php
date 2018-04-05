#!/usr/bin/env php
<?php

$_GET['seed'] = 3810;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'libs/classloader.php';
require_once 'BranchGenerator.php';

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

if(PHP_SAPI === 'cli')
{
  $file = new NBT\CompressedWriter(new \SplFileObject(__DIR__ . "/branch.schematic", 'wb'));
}
else
{
  header('Content-Type: application/x-minecraft-schematic');
  header('Content-Disposition: attachment; filename="branch.schematic"');
  $file = new NBT\CompressedWriter(new \SplFileObject("php://stdout", 'ab'));
}

$file->write($nbt);
unset($file);
