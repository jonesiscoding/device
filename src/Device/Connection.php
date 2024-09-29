<?php

/**
 * Connection.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Device;

use DevCoding\Hints\Hint\ECT;
use DevCoding\Hints\Hint\RemoteAddr;
use DevCoding\Hints\Hint\SaveData;

/**
 * Object class representing the connection of a device.
 *
 * @author  AMJones <am@jonesiscoding.com
 * @license https://github.com/jonesiscoding/code-object/blob/main/LICENSE
 */
class Connection extends DeviceChild
{
  /**
   * Returns the Effective Connection type: 4g, 3g, 2g, or slow-2g, as taken from the ECT header, or alternately the
   * 'ect' key of the device.js cookie.
   *
   * @see ECT
   * @return string
   */
  public function getEffectiveType(): string
  {
    return $this->ClientHints->get(ECT::HEADER);
  }

  /**
   * Returns the IP address of the device, or a default value if the IP cannot be found.
   *
   * @see RemoteAddr
   * @return string
   */
  public function getRemoteAddress(): string
  {
    return $this->ClientHints->get(RemoteAddr::HEADER);
  }

  /**
   * Evaluates if the app should attempt to save data in responses to the device.  This can be due to user preference,
   * connection type, and other aspects of the device connection.
   *
   * Value is based on the 'Save-Data' header, the ECT header, and other items in the legacy 'Navigator' javascript API.
   *
   * @see SaveData
   * @return bool
   */
  public function isSaveData()
  {
    return $this->ClientHints->bool(SaveData::HEADER);
  }
}
