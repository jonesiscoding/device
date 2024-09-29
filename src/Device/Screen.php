<?php

namespace DevCoding\Device;

/**
 * Screen.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevCoding\Client\Object\Hardware\Pointers;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\Height;
use DevCoding\Hints\Hint\Width;
use DevCoding\Hints\Hint\Pointers as PointersHint;

/**
 * Object representing the screen of a device.
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
class Screen extends DeviceChild
{
  /**
   * Returns the DPR of the screen (if it can be determined) else a default value.
   *
   * @see DPR
   * @return float
   */
  public function getDevicePixelRatio()
  {
    return $this->ClientHints->get(DPR::HEADER);
  }

  /**
   * Returns the height of the screen (if it can be determined) in pixels else a default value.  Note that this is the
   * maximum height, not the usable height or viewport height.
   *
   * @see Height
   * @return float
   */
  public function getHeight()
  {
    return $this->ClientHints->get(Height::HEADER);
  }

  /**
   * Returns an object representing the pointers available on the screen if available, or null if they cannot be detected.
   *
   * @see PointersHint
   * @return Pointers|null
   */
  public function getPointers()
  {
    $pointers = $this->ClientHints->array(PointersHint::HEADER);
    if (!empty($pointers))
    {
      $primary = array_shift($pointers);

      return new Pointers($primary, $pointers);
    }

    return null;
  }

  /**
   * Returns the width of the screen (if it can be determined) in pixels else a default value.  Note that this is the
   * maximum width, not the usable width or viewport width.
   *
   * @see Height
   * @return float
   */
  public function getWidth(): float
  {
    return $this->ClientHints->get(Width::HEADER);
  }
}
