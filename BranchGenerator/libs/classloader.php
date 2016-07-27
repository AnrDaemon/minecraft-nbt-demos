<?php
/** Universal stackable classloader.
*
* @version SVN: $Id$
*/

spl_autoload_register(function($className){
  $file = new SplFileInfo(__DIR__ . strtr(substr("$className.php", 9), '\\', '/'));
  $path = $file->getRealPath();
  if(!empty($path))
  {
    include_once $path;
  }
});
