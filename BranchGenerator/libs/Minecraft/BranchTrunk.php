<?php
/** Branch trunk primitive.
* @version SVN: $Id$
*/

namespace AnrDaemon\Minecraft;

use
  AnrDaemon\Math\Point,
  AnrDaemon\Misc\MtRand;

class BranchTrunk
implements Interfaces\BranchTrunk
{
  public $p0;
  public $p1;
  public $dir;
  public $len;
  public $r0;
  public $r1;
  public $ap;
  public $av;
  public $red;
  public $blue;

  public $stem;
  public $branch;

  public static function create(Point $p0, Point $dir, $r0, $r1)
  {
    return new static($p0, $dir, $dir->vl, $r0, $r1);
  }

  public function __construct(Point $p0, Point $dir, $len, $r0, $r1)
  {
    $vl = $dir->distance(0, 0, 0);
    if($vl == 1)
    {
      $this->ap = atan2($dir->y, $dir->x);
      $this->av = asin($dir->z);
      $this->dir = clone $dir;
    }
    else
    {
      $this->ap = atan2($dir->y, $dir->x);
      $this->av = asin($dir->z / $vl);
      $this->dir = Point::fromPolar(1, $this->ap, $this->av);
    }

    $this->p0 = clone $p0;
    $this->p1 = $p0->translate($len * $this->dir->x, $len * $this->dir->y, $len * $this->dir->z);
    $this->len = $len;
    $this->r0 = $r0;
    $this->r1 = $r1;

    $cap = cos($this->ap - M_PI / 2);
    $sap = sin($this->ap - M_PI / 2);
    $sav = sin($this->av - M_PI / 2);

    $points[] = Point::fromCartesian(
        $this->p0->x + $this->r0 * $cap,
        $this->p0->y + $this->r0 * $sap,
        $this->p0->z + $this->r0 * $sav
      );
    $points[] = Point::fromCartesian(
        $this->p0->x - $this->r0 * $cap,
        $this->p0->y - $this->r0 * $sap,
        $this->p0->z - $this->r0 * $sav
      );
    $points[] = Point::fromCartesian(
        $this->p1->x + $this->r1 * $cap,
        $this->p1->y + $this->r1 * $sap,
        $this->p1->z + $this->r1 * $sav
      );
    $points[] = Point::fromCartesian(
        $this->p1->x - $this->r1 * $cap,
        $this->p1->y - $this->r1 * $sap,
        $this->p1->z - $this->r1 * $sav
      );

    $this->red = Point::fromCartesian(
        floor(min($points[0]->x, $points[1]->x, $points[2]->x, $points[3]->x)),
        floor(min($points[0]->y, $points[1]->y, $points[2]->y, $points[3]->y)),
        floor(min($points[0]->z, $points[1]->z, $points[2]->z, $points[3]->z))
      );

    $this->blue = Point::fromCartesian(
        ceil(max($points[0]->x, $points[1]->x, $points[2]->x, $points[3]->x)),
        ceil(max($points[0]->y, $points[1]->y, $points[2]->y, $points[3]->y)),
        ceil(max($points[0]->z, $points[1]->z, $points[2]->z, $points[3]->z))
      );
  }

  /** Spawn a new branch from the given coords.
  *
  */
  private function branch(self $branch, $range)
  {
    $ap = $branch->ap + $range['yaw'] * MtRand::f();
    $av = $branch->av + $range['roll'] * MtRand::f();
    $len = max(1, $branch->len + $range['length'] * MtRand::p());
    $dia = max(1, 2 * $branch->r1 + $range['dia'] * MtRand::p());
    return new self($branch->p1,
      Point::fromPolar(1, $ap, $av),
      $len, $branch->r1, $dia / 2);
  }

  public function next()
  {
    $q0 = array();

    if(isset($this->stem))
    {
      $trunk = $this->branch($this, $this->stem['range']);
      $q0[] = $trunk;

      if(isset($this->branch) && (2 * ($trunk->r1 + $this->branch['force']['dia'])) > $this->branch['limit']['dia'])
      {
        $trunk->stem = clone $this->stem;
        $trunk->branch = clone $this->branch;
      }
    }

    if(isset($this->branch))
    {
      $twist = clone $this;
      $twist->r1 += $this->branch['force']['dia'];
      $twist->len *= $this->branch['force']['length'];

      $twist->ap += $this->branch['force']['yaw'];
      if(MtRand::p() > 0)
      {
        $trunk = $this->branch($twist, $this->branch['range']);
        $q0[] = $trunk;

        if((2 * ($trunk->r1 + $this->branch['force']['dia'])) > $this->branch['limit']['dia'])
        {
          $trunk->stem = clone $this->stem;
          $trunk->branch = clone $this->branch;
        }
      }

      $twist->ap -= 2 * $this->branch['force']['yaw'];
      if(MtRand::p() > 0)
      {
        $trunk = $this->branch($twist, $this->branch['range']);
        $q0[] = $trunk;

        if((2 * ($trunk->r1 + $this->branch['force']['dia'])) > $this->branch['limit']['dia'])
        {
          $trunk->stem = clone $this->stem;
          $trunk->branch = clone $this->branch;
        }
      }
    }

    return $q0;
  }

/*

Equation of the plane via point P0(x0, y0, z0) and vector d(xd, yd, zd):
xd * (x - x0) + yd * (y - y0) + zd * (z - z0) = 0

Converted to normal form:
xd * x + yd * y + zd * z - (xd * x0 + yd * y0 + zd * z0) = 0

Distance from a point P(x, y, z) to the abovementioned plane:
L = abs(xd * x + yd * y + zd * z - (xd * x0 + yd * y0 + zd * z0)) / sqrt(xd * xd + yd * yd + zd * zd)

Distance from a point P(x, y, z) to the line defined via two points P0(x0, y0, z0) and P1(x1, y1, z1):
L = sqrt(
  (
    ((x - x0) * (y1 - y0) - (y - y1) * (x1 - x0)) ^ 2 +
    ((y - y0) * (z1 - z0) - (z - z1) * (y1 - y0)) ^ 2 +
    ((z - z0) * (x1 - x0) - (x - x1) * (z1 - z0)) ^ 2 +
  )
  /
  (x1 - x0) ^ 2 + (y1 - y0) ^ 2 + (z1 - z0) ^ 2
)

Converted to a vector defining the abovementioned plane:
L = sqrt(
  (
    ((x - x0) * yd - (y - y0) * xd) ^ 2 +
    ((y - y0) * zd - (z - z0) * yd) ^ 2 +
    ((z - z0) * xd - (x - x0) * zd) ^ 2 +
  )
  /
  (xd * xd + yd * yd + zd * zd)
)

.plan:
1. Find L2 to line P0d.
2. If L2 > R0, then fail.
3. Find distance L0 to plane P0d.
4. If L0 > P0P1, then fail.
5. Find distance L1 to plane P1d.
6. If L1 > P0P1, then fail.
7. Find R as (R0 + (R1 - R0) * L0 / P0P1).
8. If L2 > R, then fail.
9. Save.

*/
  private $k;
  final public function toVoxel($x, $y = null, $z = null)
  {
    if($x instanceof Point)
    {
      $z = $x->z;
      $y = $x->y;
      $x = $x->x;
    }

    if(!isset($this->k))
      $this->k = sqrt($this->dir->x * $this->dir->x + $this->dir->y * $this->dir->y + $this->dir->z * $this->dir->z);

    $L2 = sqrt(
        pow(($x - $this->p0->x) * $this->dir->y - ($y - $this->p0->y) * $this->dir->x, 2) +
        pow(($y - $this->p0->y) * $this->dir->z - ($z - $this->p0->z) * $this->dir->y, 2) +
        pow(($z - $this->p0->z) * $this->dir->x - ($x - $this->p0->x) * $this->dir->z, 2)
      ) / $this->k;

    if($L2 <= $this->r0)
    {
      $c = $this->dir->x * $x + $this->dir->y * $y + $this->dir->z * $z;

      $L0 = abs($c - ($this->dir->x * $this->p0->x + $this->dir->y * $this->p0->y + $this->dir->z * $this->p0->z)) / $this->k;

      if($L0 <= $this->len)
      {
        $L1 = abs($c - ($this->dir->x * $this->p1->x + $this->dir->y * $this->p1->y + $this->dir->z * $this->p1->z)) / $this->k;

        if($L1 <= $this->len)
        {
          $R = $this->r0 + ($this->r1 - $this->r0) * $L0 / $this->len;

          if($L2 <= $R)
          {
            return 1;
          }
        }
      }
    }
    return 0;
  }
}
