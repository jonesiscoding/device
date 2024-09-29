<?php

namespace DevCoding\Device;

/**
 * DeviceChild.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevCoding\Hints\ClientHints;

/**
 * Base class for objects that are children of the device object, allowing access to the ClientHints property.
 *
 * @author  AMJones <am@jonesiscoding.com
 * @license https://github.com/jonesiscoding/code-object/blob/main/LICENSE
 */
abstract class DeviceChild
{
  /** @var ClientHints */
  protected $ClientHints;

  /**
   * @param ClientHints $ClientHints
   */
  public function __construct(ClientHints $ClientHints)
  {
    $this->ClientHints = $ClientHints;
  }
}
