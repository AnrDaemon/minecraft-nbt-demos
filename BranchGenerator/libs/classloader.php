<?php
/** Universal stackable classloader.
*
* @version SVN: $Id$
*/

namespace AnrDaemon;

return \call_user_func(
  function()
  {
    $nsl = \strlen(__NAMESPACE__);
    return \spl_autoload_register(
      function($className)
      use($nsl)
      {
        if(\strncmp($className, __NAMESPACE__, $nsl) !== 0)
          return;

        $className = \substr($className, $nsl);
        if($className[0] !== "\\")
          return;

        $path = __DIR__ . \strtr("$className.php", '\\', '/');
        if(\file_exists($path))
        {
          return include_once $path;
        }
      }
    );
  }
);
