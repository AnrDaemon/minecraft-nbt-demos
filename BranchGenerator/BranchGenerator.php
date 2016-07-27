<?php

use AnrDaemon\Misc\Coordinate3D as Coords, AnrDaemon\Misc\MtRand,
  AnrDaemon\Minecraft\BranchTrunk,
  AnrDaemon\Minecraft\BranchTwister;

if(!isset($_GET['seed']))
  header("Location: {$_SERVER['PHP_SELF']}?seed=" . rand(), true, 302);

mt_srand($_GET['seed']);

$p0 = new Coords(632, 53, 40);
$ap = deg2rad(-90);
$av = deg2rad(2);
$len = 28;
$r0 = 4;
$r1 = 3;

$dx = 0;
$dy = 256;

$stem = new BranchTwister();
$stem->setRange(array(
    'yaw' => deg2rad(7),
    'roll' => deg2rad(3),
    'length' => -5,
    'dia' => -1.5
  ))
  ->setLimit(array('dia' => 1));

$branch = new BranchTwister();
$branch->setRange(array(
    'yaw' => deg2rad(6),
    'roll' => deg2rad(3),
    'length' => -7,
    'dia' => -1,
  ))
  ->setForce(array(
    'yaw' => deg2rad(50),
    'length' => .80,
    'dia' => -.7,
  ))
  ->setLimit(array('dia' => 1));

$trunks[0] = new BranchTrunk($p0, Coords::fromPolar(1, $ap, $av), $len, $r0, $r1);
$trunks[0]->branch = $branch;
$trunks[0]->stem = $stem;

unset($branch, $stem);

$q0[] = $trunks[0];

$white = new Coords(round($p0['x']), round($p0['y']), round($p0['z']));
$red = clone $trunks[0]->red;
$blue = clone $trunks[0]->blue;

for($n = 0; $n < 10; $n++)
{
  $q = $q0; $q0 = array();
  foreach($q as $joint)
  {
    $_t = $joint->next();
    foreach($_t as $trunk)
    {
      $trunks[] = $trunk;
      $q0[] = $trunk;

      // Expand schematic bounding box.
      $red['x'] = min($red['x'], $trunk->red['x']);
      $red['y'] = min($red['y'], $trunk->red['y']);
      $red['z'] = min($red['z'], $trunk->red['z']);
      $blue['x'] = max($blue['x'], $trunk->blue['x']);
      $blue['y'] = max($blue['y'], $trunk->blue['y']);
      $blue['z'] = max($blue['z'], $trunk->blue['z']);
    }
  }
}

$dims = new Coords(1 + $blue['x'] - $red['x'], 1 + $blue['y'] - $red['y'], 1 + $blue['z'] - $red['z']);
