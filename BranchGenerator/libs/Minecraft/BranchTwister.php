<?php
/** Branch twisting preset.
* @version SVN: $Id: BranchTwister.php 268 2018-03-18 20:49:20Z anrdaemon $
*/

namespace AnrDaemon\Minecraft;

class BranchTwister
implements \ArrayAccess
{
  protected $_props = array(
    'range' => array(
        'yaw' => 0,
        'roll' => 0,
        'length' => 0,
        'dia' => 0,
      ),
    'force' => array(
        'yaw' => 0,
        'roll' => 0,
        'length' => 0,
        'dia' => 0,
      ),
    'limit' => array(
        'yaw' => 0,
        'roll' => 0,
        'length' => 0,
        'dia' => 0,
      )
    );

  protected function set($set, $yaw, $roll = null, $length = null, $dia = null)
  {
    if(isset($this->_props[$set]))
    {
      if(is_array($yaw))
        foreach($yaw as $key => $value)
        {
          if(isset($this->_props[$set][$key]))
          {
            if(is_numeric($value))
              $this->_props[$set][$key] = (float)$value;
            else
              throw new \LogicException("Value must be numeric for \`$set:$key'.");
          }
        }
      else
      {
        foreach(array('yaw', 'roll', 'length', 'dia') as $key)
        {
          if(isset($$key))
          {
            if(is_numeric($$key))
              $this->_props[$set][$key] = (float)$$key;
            else
              throw new \LogicException("Value must be numeric for \`$set:$key'.");
          }
        }
      }
    }
    else
      throw new \LogicException("Unknown parameter set \`$set'.");

    return $this;
  }

  public function setRange($yaw, $roll = null, $length = null, $dia = null)
  {
    return $this->set('range', $yaw, $roll, $length, $dia);
  }

  public function setForce($yaw, $roll = null, $length = null, $dia = null)
  {
    return $this->set('force', $yaw, $roll, $length, $dia);
  }

  public function setLimit($yaw, $roll = null, $length = null, $dia = null)
  {
    return $this->set('limit', $yaw, $roll, $length, $dia);
  }

// \ArrayAccess implementation for coordinates.

  public function offsetExists($offset)
  {
    return isset($this->_props[$offset]);
  }

  public function offsetGet($offset)
  {
    return $this->_props[$offset];
  }

  public function offsetSet($offset, $value)
  {
    if(!isset($this->_props[$offset]))
      throw new \LogicException('Property does not exist.');

    if(!is_array($value))
      throw new \LogicException('Value must be an array for indirect call.');

    $this->set($offset, $value);
  }

  public function offsetUnset($offset)
  {
    throw new \LogicException('Forbidden.');
  }
}
