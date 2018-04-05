#!/usr/bin/env php -f
<?php

$_GET['seed'] = 3810;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'libs/classloader.php';
require_once 'BranchGenerator.php';

$dxp = round((512 - $dims->x) / 2 - $red->x);
$dyp = round((384 - $dims->y) / 2 - $red->y);

$dxs = round((512 - $dims->y) / 2 - $red->y);
$dys = 512 - round(((128 - $dims->z) / 2 - $red->z));

$canvas = imagecreatetruecolor(512, 512);

$bg = imagecolorallocate($canvas, 15, 127, 31);
imagefill($canvas, 1, 1, $bg);

$fg = imagecolorallocate($canvas, 127, 63, 31);

foreach($trunks as $trunk)
{
  $a1 = $trunk->ap - M_PI / 2;
  $a2 = $a1 + M_PI;
  $points[] = $dxp + round($trunk->p0->x + $trunk->r0 * cos($a1));
  $points[] = $dyp + round($trunk->p0->y + $trunk->r0 * sin($a1));
  $points[] = $dxp + round($trunk->p0->x + $trunk->r0 * cos($a2));
  $points[] = $dyp + round($trunk->p0->y + $trunk->r0 * sin($a2));
  $points[] = $dxp + round($trunk->p1->x + $trunk->r1 * cos($a2));
  $points[] = $dyp + round($trunk->p1->y + $trunk->r1 * sin($a2));
  $points[] = $dxp + round($trunk->p1->x + $trunk->r1 * cos($a1));
  $points[] = $dyp + round($trunk->p1->y + $trunk->r1 * sin($a1));

  imagefilledpolygon($canvas, $points, (int)(count($points) / 2), $fg);
  unset($points);

  $a1 = - $trunk->av - M_PI / 2;
  $a2 = $a1 + M_PI;
  $points[] = $dxs + round($trunk->p0->y + $trunk->r0 * cos($a1));
  $points[] = $dys - round($trunk->p0->z + $trunk->r0 * sin($a1));
  $points[] = $dxs + round($trunk->p0->y + $trunk->r0 * cos($a2));
  $points[] = $dys - round($trunk->p0->z + $trunk->r0 * sin($a2));
  $points[] = $dxs + round($trunk->p1->y + $trunk->r1 * cos($a2));
  $points[] = $dys - round($trunk->p1->z + $trunk->r1 * sin($a2));
  $points[] = $dxs + round($trunk->p1->y + $trunk->r1 * cos($a1));
  $points[] = $dys - round($trunk->p1->z + $trunk->r1 * sin($a1));

  imagefilledpolygon($canvas, $points, (int)(count($points) / 2), $fg);
  unset($points);
}

header('Content-Type: image/png');

if(PHP_SAPI === 'cli')
  imagepng($canvas, __DIR__ . "/branch.png");
else
  imagepng($canvas);
