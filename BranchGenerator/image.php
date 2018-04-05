#!/usr/bin/env php -f
<?php

use AnrDaemon\Math\Point;

$_GET['seed'] = 3810;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'libs/classloader.php';
require_once 'BranchGenerator.php';

$dxp = round((512 - $dims->x) / 2 - $red->x);
$dyp = round((384 - $dims->y) / 2 - $red->y);

$dxs = round((512 - $dims->y) / 2 - $red->y);
$dys = 512 - round(((128 - $dims->z) / 2 - $red->z));

$planOff = 96;
$sideOff = 128;
$offp = Point::fromCartesian(256, 256 -$planOff, 0);
$offs = Point::fromCartesian(256 -$sideOff, 256 +$sideOff, 0);
$dz = 448;

$canvas = imagecreatetruecolor(512, 512);

$bg = imagecolorallocate($canvas, 15, 127, 31);
imagefill($canvas, 1, 1, $bg);

$fg = imagecolorallocate($canvas, 127, 63, 31);

foreach($trunks as $trunk)
{
  $p0 = $trunk->p0->translate($offp);
  $p1 = $trunk->p1->translate($offp);

  $dir1 = $trunk->dir->rotate(M_PI / 2, 0);
  $dir2 = $trunk->dir->rotate(-M_PI / 2, 0);

  $pA = $p0->translate($dir1, $trunk->r0);
  $pB = $p0->translate($dir2, $trunk->r0);
  $pC = $p1->translate($dir2, $trunk->r1);
  $pD = $p1->translate($dir1, $trunk->r1);

  $points = [
    floor($pA->x), floor($pA->y),
    floor($pB->x), floor($pB->y),
    floor($pC->x), floor($pC->y),
    floor($pD->x), floor($pD->y),
  ];

  imagefilledpolygon($canvas, $points, (int)(count($points) / 2), $fg);
  unset($points);

  $dir1 = $trunk->dir->rotate(0, M_PI / 2);
  $dir2 = $trunk->dir->rotate(0, -M_PI / 2);

  $p0 = $trunk->p0->translate($offs);
  $p1 = $trunk->p1->translate($offs);
  $d0 = 2 * floor($trunk->r0) - 1;
  $d1 = 2 * floor($trunk->r1) - 1;

  $pA = $p0->translate($dir1, $trunk->r0);
  $pB = $p0->translate($dir2, $trunk->r0);
  $pC = $p1->translate($dir2, $trunk->r1);
  $pD = $p1->translate($dir1, $trunk->r1);

  // FIXME: This is NOT totally correct, but close enough for a visual display.
  $pointsX[] = floor($pA->x);
  $pointsY[] = floor($pA->y);
  $pointsX[] = $pointsY[] = $dz - floor($pA->z);

  $pointsX[] = floor($pB->x);
  $pointsY[] = floor($pB->y);
  $pointsX[] = $pointsY[] = $dz - floor($pB->z);

  $pointsX[] = floor($pC->x);
  $pointsY[] = floor($pC->y);
  $pointsX[] = $pointsY[] = $dz - floor($pC->z);

  $pointsX[] = floor($pD->x);
  $pointsY[] = floor($pD->y);
  $pointsX[] = $pointsY[] = $dz - floor($pD->z);

  imagefilledpolygon($canvas, $pointsX, (int)(count($pointsX) / 2), $fg);
  imagefilledellipse($canvas, round($p0->x), $dz - round($p0->z), $d0, $d0, $fg);
  imagefilledellipse($canvas, round($p1->x), $dz - round($p1->z), $d1, $d1, $fg);
  imagefilledpolygon($canvas, $pointsY, (int)(count($pointsY) / 2), $fg);
  imagefilledellipse($canvas, round($p0->y), $dz - round($p0->z), $d0, $d0, $fg);
  imagefilledellipse($canvas, round($p1->y), $dz - round($p1->z), $d1, $d1, $fg);
  unset($pointsX, $pointsY);
}

header('Content-Type: image/png');

if(PHP_SAPI === 'cli')
  imagepng($canvas, __DIR__ . "/branch.png");
else
  imagepng($canvas);
