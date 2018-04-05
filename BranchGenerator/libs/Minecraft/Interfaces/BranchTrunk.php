<?php
/** Branch trunk primitive.
* @version SVN: $Id$
*/

namespace AnrDaemon\Minecraft\Interfaces;

use
  AnrDaemon\Math\Point;

interface BranchTrunk
{
  public static function create(Point $p0, Point $dir, $r0, $r1);
  public function next();
  public function toVoxel($x, $y = null, $z = null);
}
