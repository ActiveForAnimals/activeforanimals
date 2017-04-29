<?php

namespace Drupal\activeforanimals;

/**
 * Provides authentication keys.
 *
 * This class is stored in the /private directory which
 * adds a layer of protection against unwanted access.
 */
class Settings {

  /**
   * Environment values.
   */
  const ENVIRONMENT_LIVE = 'live';
  const ENVIRONMENT_TEST = 'test';
  const ENVIRONMENT_DEV = 'dev';


  /**
   * The path to the json settings file.
   *
   * @var string
   */
  private static $jsonpath = 'sites/default/files/private/keys.json';

  /**
   * Get settings.
   *
   * @return array
   *   Settings array.
   */
  private static function getSettings() {
    $json_keys = file_get_contents(self::$jsonpath);
    $data = json_decode($json_keys, TRUE);
    return $data;
  }

  /**
   * Return API key for Google services.
   *
   * @return string
   *   The API key.
   */
  public static function getGoogleApiKey() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['google']['maps']) ? $data['google']['maps'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['google']['maps']) ? $data['google']['maps'] : NULL;
          break;

        case self::ENVIRONMENT_DEV:
          $key = isset($data['google']['maps']) ? $data['google']['maps'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return e-mail address for beta signup.
   *
   * @return string
   *   The API key.
   */
  public static function getBetaSignupRecipient() {
    $email_address = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $email_address = isset($data['beta_signup']['recipient']) ? $data['beta_signup']['recipient'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $email_address = isset($data['beta_signup']['recipient']) ? $data['beta_signup']['recipient'] : NULL;
          break;

        case self::ENVIRONMENT_DEV:
          $email_address = isset($data['beta_signup']['recipient']) ? $data['beta_signup']['recipient'] : NULL;
          break;

      }
    }
    return $email_address;
  }

}
