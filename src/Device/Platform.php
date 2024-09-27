<?php

namespace DevCoding\Device;

use DevCoding\Client\Object\Platform\PlatformImmutable;
use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Client\Resolver\Platform\LinuxMatcher;
use DevCoding\Hints\Hint\Arch;
use DevCoding\Hints\Hint\Bitness;
use DevCoding\Hints\Hint\PlatformVersion;
use DevCoding\Hints\Hint\Platform as PlatformHint;

class Platform extends DeviceChild
{
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
   * @return string|null
   */
  public function getArch()
  {
    return $this->ClientHints->get(Arch::HEADER);
  }

  /**
   * @return int|string|null
   */
  public function getBitness()
  {
    return $this->ClientHints->get(Bitness::HEADER);
  }

  /**
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
   * @return string
   */
  public function getName()
  {
    return $this->ClientHints->get(PlatformHint::HEADER);
  }

  /**
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
