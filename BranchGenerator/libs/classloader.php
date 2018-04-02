<?php
/** Universal stackable classloader.
*
* @version SVN: $Id: classloader.php 268 2018-03-18 20:49:20Z anrdaemon $
*/

namespace AnrDaemon;

return \call_user_func(function(){
  $nsl = \strlen(__NAMESPACE__);
  return \spl_autoload_register(
    function($className)
    use($nsl)
    {
      if(\strncmp($className, __NAMESPACE__, $nsl) !== 0)
        return;

      $className = \substr($className, $nsl);
      if(\strlen($className) < 2)
        return;

      $path = \realpath(__DIR__ . \strtr("$className.php", '\\', '/'));
      if(!empty($path))
      {
        return include_once $path;
      }
    }
  );
});
