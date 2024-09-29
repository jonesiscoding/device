<?php

/**
 * Device.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DevCoding\Device;

use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\ClientHints;
use DevCoding\Hints\Hint\DeviceMemory;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\ECT;
use DevCoding\Hints\Hint\Height;
use DevCoding\Hints\Hint\Model;
use DevCoding\Hints\Hint\Width;

/**
 * Object class representing the device making an HTTP(s) request.
 *
 * @author  AMJones <am@jonesiscoding.com
 * @license https://github.com/jonesiscoding/code-object/blob/main/LICENSE
 */
class Device
{
  /** @var ServiceBag */
  protected $container;

  /**
   * @param ConfigBag $ConfigBag
   */
  public function __construct(ConfigBag $ConfigBag)
  {
    $this->container = new ServiceBag([ConfigBag::class => $ConfigBag]);

    if ($this->container->get(ConfigBag::class)->get('warm') && 'cli' !== php_sapi_name())
    {
      $this->getClientHints()->warm();
    }
  }

  /**
   * @param array $config
   *
   * @return static
   */
  public static function create($config = [])
  {
    return new static(new ConfigBag($config));
  }

  // region //////////////////////////////////////////////// Hardware Getters

  /**
   * Returns the device model name (if available in the 'Sec-CH-UA-Model' header) else a default value.
   *
   * @see Model
   *
   * @return string|null
   */
  public function getModel()
  {
    return $this->getClientHints()->get(Model::HEADER);
  }

  /**
   * Returns a coarse value reflecting the amount of RAM in Gigabytes available to the device (if available in the
   * 'Device-Memory' header) else a default value.
   *
   * @see DeviceMemory
   * @return float
   */
  public function getDeviceMemory()
  {
    return $this->getClientHints()->get(DeviceMemory::HEADER);
  }

  /**
   * Returns the DPR of the device (if it can be determined) else a default value.
   *
   * @deprecated Use Screen:getDevicePixelRatio
   * @return float
   */
  public function getDevicePixelRatio()
  {
    return $this->getClientHints()->get(DPR::HEADER);
  }

  /**
   * @deprecated Use Connection::getEffectiveConnectionType
   * @return string
   */
  public function getEffectiveConnectionType()
  {
    return $this->getClientHints()->get(ECT::HEADER);
  }

  /**
   * @deprecated Use Screen::getHeight
   * @return float|int
   */
  public function getHeight()
  {
    return $this->getClientHints()->get(Height::HEADER);
  }

  /**
   * @deprecated Use Screen::getWidth
   * @return float|int
   */
  public function getWidth()
  {
    return $this->getClientHints()->get(Width::HEADER);
  }

  // endregion ///////////////////////////////////////////// End Hardware Getters

  // region //////////////////////////////////////////////// Subset Getters

  /**
   * Returns an object representing the software used on the device.
   *
   * @return Client
   */
  public function Client(): Client
  {
    return $this->get(Client::class);
  }

  /**
   * Returns an object representing the screen on the device.  For devices with more than one screen, only the screen
   * containing the client software at the time of the last request is reflected.
   *
   * @return Screen
   */
  public function Screen(): Screen
  {
    return $this->get(Screen::class);
  }

  /**
   * Returns an object representing the platform used on the device.
   *
   * @return Platform
   */
  public function Platform(): Platform
  {
    return $this->get(Platform::class);
  }

  /**
   * Returns an object representing the preferences of the user using the device.
   *
   * @return Preferences
   */
  public function Preferences(): Preferences
  {
    return $this->get(Preferences::class);
  }

  // endregion ///////////////////////////////////////////// End Subset Getters

  // region //////////////////////////////////////////////// Requirement Methods

  /**
   * Evaluates if the current client lacks one or more of the features indiciated as required by the configuration.
   * If polyfills are specified in the configuration, they are taken into account when evaluating the client.
   *
   * @return bool
   */
  public function isSunset(): bool
  {
    /** @var ConfigBag $ConfigBag */
    $ConfigBag = $this->container->get(ConfigBag::class);
    $required  = $ConfigBag->getRequire();
    if (!empty($required))
    {
      $polyfills = $ConfigBag->getPolyfill();
      foreach($ConfigBag->get('require') as $key => $value)
      {
        if (!$this->isValid($key, $value))
        {
          if (!in_array($key, $polyfills))
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Evaluates if the configured polyfills are needed, based on the 'require' and 'polyfill' configurations.
   *
   * @return bool
   */
  public function isPolyfill(): bool
  {
    $ConfigBag = $this->container->get(ConfigBag::class);
    $required  = $ConfigBag->getRequire();
    if (!empty($required))
    {
      $polyfills = $ConfigBag->getPolyfill();
      foreach($required as $key => $value)
      {
        if (!$this->isValid($key, $value))
        {
          if (in_array($key, $polyfills))
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  // endregion ///////////////////////////////////////////// End Requisite Methods

  // region //////////////////////////////////////////////// Helper Methods

  /**
   * Returns a service from the service container, instantiating that service as needed.
   *
   * @param string $id The fully qualified class name of the service object.
   *
   * @return mixed|object
   */
  protected function get($id)
  {
    return $this->container->assert($id)->get($id);
  }

  /**
   * Returns the ClientHints object.  If that object is not present in the service container, it will be created
   * using the configuration given at instantiation of this device.
   *
   * @return ClientHints
   */
  public function getClientHints(): ClientHints
  {
    if (!$this->container->has(ClientHints::class))
    {
      $config = $this->container->get(ConfigBag::class);
      $header = $this->container->assert(HeaderBag::class)->get(HeaderBag::class);

      if ($config->has('cookie'))
      {
        $cookie = $this->container->assert(new CookieBag($config->get('cookie')))->get(CookieBag::class);
      }
      else
      {
        $cookie = $this->container->assert(CookieBag::class)->get(CookieBag::class);
      }

      $this->container->assert(new ClientHints($config, $header, $cookie));
    }

    return $this->get(ClientHints::class);
  }

  /**
   * Evaluates if the device has all the features or hint values given in the 'require' key of the configuration.
   *
   * @param string $key
   * @param mixed $expected
   *
   * @return bool
   */
  protected function isValid($key, $expected): bool
  {
    $ClientHints = $this->getClientHints();
    if ($ClientHints->has($key))
    {
      $hint = is_bool($expected) ? $ClientHints->bool($key) : $ClientHints->get($key);
      if (is_bool($expected))
      {
        return ($hint === $expected);
      }
      elseif (is_numeric($expected))
      {
        return $hint >= $expected;
      }
      elseif (is_string($expected))
      {
        return $hint == $expected;
      }
      else
      {
        return !empty($value);
      }
    }

    return false;
  }

  // endregion ///////////////////////////////////////////// End Helper Methods
}
