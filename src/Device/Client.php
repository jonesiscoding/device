<?php

/**
 * Client.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Device;

use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Object\Headers\UA;
use DevCoding\Client\Object\Headers\UAFullVersionList;
use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Hints\Hint\FullVersionList;
use DevCoding\Hints\Hint\ViewportWidth;
use DevCoding\Hints\Hint\ViewportHeight;
use DevCoding\Hints\Hint\UserAgent;

/**
 * Object class representing the client software of a device.
 *
 * @author  AMJones <am@jonesiscoding.com
 * @license https://github.com/jonesiscoding/code-object/blob/main/LICENSE
 */
class Client extends DeviceChild
{
  /**
   * Convenience function to return a Browser object.
   *
   * @return Browser
   */
  public function getBrowser()
  {
    return $this->ClientHints->browser();
  }

  /**
   * Returns a UAFullVersionList object, with the fully parsed contents of the 'Sec-CH-UA-Full-Version-List' header.
   *
   * @return UAFullVersionList|null
   */
  public function getFullVersionList()
  {
    if ($fvl = $this->ClientHints->get(FullVersionList::HEADER))
    {
      return new UAFullVersionList($fvl);
    }

    return null;
  }

  /**
   * Returns a UA object, with the fully parsed contents of the 'Sec-CH-UA' header.
   *
   * @return UA|null
   */
  public function getUserAgent()
  {
    if ($ua = $this->ClientHints->get(UserAgent::HEADER))
    {
      return new UA($ua);
    }

    return null;
  }

  /**
   * Returns a ClientVersion object, taken from the 'Sec-CH-UA-Full-Version-List' or 'Sec-CH-UA' headers.
   *
   * @return ClientVersion|null
   */
  public function getVersion()
  {
    if ($full = $this->getFullVersionList())
    {
      return $full->getVersion();
    }
    elseif ($ua = $this->getUserAgent())
    {
      return new ClientVersion($ua->getVersion());
    }

    return null;
  }

  /**
   * If available, returns the current viewport height in pixels. Note that this value is only accurate if the user has
   * not resized the window since the last request.
   *
   * Value is taken from the 'vh' key of the cookie set by device.js.
   *
   * @return float|int
   */
  public function getViewportHeight()
  {
    return $this->ClientHints->get(ViewportHeight::HEADER);
  }

  /**
   * If available, returns the current viewport with in pixels.  Note that this value is only accurate if the user has
   * not resized the window since the last request.
   *
   * Value is taken from the Sec-CH-Viewport-Width or Viewport-Width headers, or alternatively from the 'vw' key of the
   * cookie set by device.js.
   *
   * @return float|int
   */
  public function getViewportWidth()
  {
    return $this->ClientHints->get(ViewportWidth::HEADER);
  }

  /**
   * Evaluates if the browser feature given (as a header) is supported by this client.
   *
   * These values are determined by information provided by the cookie set by device.js, or alternatively from CanIUse
   * data, based on the browser detected.
   *
   * @param string $key
   *
   * @return bool
   */
  public function isSupported($key): bool
  {
    return $this->ClientHints->bool($key);
  }
}
