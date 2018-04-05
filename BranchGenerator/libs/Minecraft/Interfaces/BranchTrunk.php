<?php
/** Branch trunk primitive.
* @version SVN: $Id$
*/

namespace AnrDaemon\Minecraft\Interfaces;

use
  AnrDaemon\Math\Point;

interface BranchTrunk
{
  public function __construct(Point $p0, Point $dir, $len, $r0, $r1);
  public function next();
  public function toVoxel($x, $y = null, $z = null);
}
