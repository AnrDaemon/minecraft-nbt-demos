<?php
/** Branch trunk primitive.
* @version SVN: $Id: BranchTrunk.php 223 2016-07-27 00:01:05Z anrdaemon $
*/

namespace AnrDaemon\Minecraft\Interfaces;

use AnrDaemon\Misc\Coordinate3D as Coords;

interface BranchTrunk
{
  public function __construct(Coords $p0, Coords $dir, $len, $r0, $r1);
  public function next();
  public function toVoxel($x, $y = null, $z = null);
}
