<?php
/** Universal stackable classloader.
*
* @version SVN: $Id$
*/

namespace AnrDaemon;

spl_autoload_register(function($className)
{
  $nl = strlen(__NAMESPACE__);
  if(strncasecmp($className, __NAMESPACE__ . '\\', $nl + 1) !== 0)
    return;

  $path = realpath(__DIR__ . strtr(substr("$className.php", $nl), '\\', '/'));
  if(!empty($path))
  {
    include_once $path;
  }
});
