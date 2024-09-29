<?php

namespace DevCoding\Device;

/**
 * Platform.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevCoding\Client\Object\Platform\PlatformImmutable;
use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Client\Resolver\Platform\LinuxMatcher;
use DevCoding\Hints\Hint\Arch;
use DevCoding\Hints\Hint\Bitness;
use DevCoding\Hints\Hint\PlatformVersion;
use DevCoding\Hints\Hint\Platform as PlatformHint;

/**
 * Object class representing the platform of a device.
 *
 * @author  AMJones <am@jonesiscoding.com
 * @license https://github.com/jonesiscoding/code-object/blob/main/LICENSE
 */
class Platform extends DeviceChild
{
  /**
   * Returns the platform name and version in the format 'Name/1.0'. For Linux devices, only the name is returned.
   *
   * @return string
   */
  public function __toString()
  {
    if (LinuxMatcher::PLATFORM !== $this->getName())
    {
      return ($obj = $this->getObject()) ? (string) $obj : 'Unknown';
    }
    else
    {
      return $this->getName();
    }
  }

  /**
   * Returns the architecture of the device if it can be determined, else a default value.
   *
   * @see Arch
   * @return string
   */
  public function getArch(): string
  {
    return $this->ClientHints->get(Arch::HEADER);
  }

  /**
   * Returns the 'bitness' of the device if it can be determined, else a default value.
   *
   * @see Bitness
   * @return int|string
   */
  public function getBitness()
  {
    return $this->ClientHints->get(Bitness::HEADER);
  }

  /**
   * Returns the platform version if it can be determined, else null.
   *
   * @see PlatformVersion
   * @return ClientVersion|null
   */
  public function getVersion()
  {
    if ($ver = $this->ClientHints->get(PlatformVersion::HEADER))
    {
      if (!empty($ver))
      {
        $obj = new ClientVersion($ver);
        if ('Windows' === $this->getName())
        {
          $maj = $obj->getMajor();
          if ($maj >= 1)
          {
            $real = $maj >= 13 ? 11 : 10;
            $ver  = preg_replace('#^' . $maj . '#', $real, $ver);
            $obj  = new ClientVersion($ver);
          }
          else
          {
            $min = $obj->getMinor();
            switch($min)
            {
              case 3:
                $obj = new ClientVersion(8.1);
                break;
              case 2:
                $obj = new ClientVersion(8.0);
                break;
              default:
                $obj = new ClientVersion(7);
                break;
            }
          }
        }

        return $obj;
      }
    }

    return null;
  }

  /**
   * Returns the platform name if it can be determined, else a default value.
   *
   * @return string
   */
  public function getName(): string
  {
    return $this->ClientHints->get(PlatformHint::HEADER);
  }

  /**
   * Returns a PlatformImmutable object to help with string representation of this object.
   *
   * @return PlatformImmutable
   */
  protected function getObject()
  {
    if ($name = $this->getName())
    {
      if ($version = $this->getVersion())
      {
        return new PlatformImmutable($name, $version);
      }
    }

    return null;
  }
}
