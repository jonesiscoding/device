<?php

namespace DevCoding\Device;

/**
 * Preferences.php
 *
 * (c) AMJones <am@jonesiscoding.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use DevCoding\Hints\Hint\ColorScheme;
use DevCoding\Hints\Hint\Contrast;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\ReducedData;
use DevCoding\Hints\Hint\ReducedMotion;
use DevCoding\Hints\Hint\ReducedTransparency;
use DevCoding\Hints\Hint\SaveData;

/**
 * Object representing preferences hinted by a device.
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
class Preferences extends DeviceChild
{
  public function getColorScheme()
  {
    return $this->ClientHints->get(ColorScheme::HEADER);
  }

  public function getContrast()
  {
    return $this->ClientHints->get(Contrast::HEADER);
  }

  /**
   * The user has indicated that they prefer dark mode through a preference on their device.
   *
   * @return bool
   */
  public function isDarkMode(): bool
  {
    return ColorScheme::DARK === $this->getColorScheme();
  }

  /**
   * The user has indicated that they prefer more contrast when viewing content and interfaces on their device.
   *
   * @return bool
   */
  public function isIncreasedContrast(): bool
  {
    return Contrast::MORE === $this->getContrast();
  }

  /**
   * The user has indicated that they prefer less contrast when viewing content and interfaces on their device.
   *
   * @return bool
   */
  public function isReducedContrast(): bool
  {
    return Contrast::LESS == $this->getContrast();
  }

  /**
   * @deprecated Use Preferences::isSaveData for better results.
   * @return bool
   */
  public function isReducedData(): bool
  {
    return $this->ClientHints->bool(ReducedData::HEADER, false);
  }

  /**
   * The user has indicated that they prefer reduced motion through a preference in the client or platform.  This
   * preference can also be automatically indicated based on device conditions such as being accessed remotely.
   *
   * @return bool
   */
  public function isReducedMotion(): bool
  {
    return $this->ClientHints->bool(ReducedMotion::HEADER, false);
  }

  /**
   * The user has indicated that they prefer reduced motion through a preference in the client or platform.
   *
   * @return bool
   */
  public function isReducedTransparency(): bool
  {
    return $this->ClientHints->bool(ReducedTransparency::HEADER, false);
  }

  /**
   * Opinionated check to determine if high resolution responsive images should be served to this device. The device
   * must not indicate a preference to save data, must have HTMLImageElement.srcset support, and must have a DPR of > 1.
   *
   * @return bool
   */
  public function isHighRes(): bool
  {
    $dpr = $this->ClientHints->get(DPR::HEADER);
    $set = $this->ClientHints->bool('HTML_IMG_SRCSET', false);

    return  $dpr > 1 && $set && !$this->isSaveData();
  }

  /**
   * The user, device, or connection provider has indicated a preference or need to save data in responses. This can be
   * via the official 'Save-Data' header, a legacy header indicating a slower mobile connection, an 'ECT' header which
   * indicates a 2G or 3G connection, or a cookie value set by device.js indicating any of the same.
   *
   * @return bool
   */
  public function isSaveData(): bool
  {
    return $this->ClientHints->bool(SaveData::HEADER, false);
  }
}
