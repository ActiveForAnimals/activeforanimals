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
   * Return API key for Google Maps.
   *
   * @return string
   *   The API key.
   */
  public static function getGoogleMapsApiKey() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['google_maps_api_key']) ? $data['google_maps_api_key'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['google_maps_api_key']) ? $data['google_maps_api_key'] : NULL;
          break;

        default:
          $key = isset($data['google_maps_api_key']) ? $data['google_maps_api_key'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return API key for Google Static Maps.
   *
   * @return string
   *   The API key.
   */
  public static function getGoogleStaticMapsApiKey() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['google_static_maps_api_key']) ? $data['google_static_maps_api_key'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['google_static_maps_api_key']) ? $data['google_static_maps_api_key'] : NULL;
          break;

        default:
          $key = isset($data['google_static_maps_api_key']) ? $data['google_static_maps_api_key'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return API key for DarkSky.
   *
   * @return string
   *   The API key.
   */
  public static function getDarkskyApiKey() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['darksky_api_key']) ? $data['darksky_api_key'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['darksky_api_key']) ? $data['darksky_api_key'] : NULL;
          break;

        default:
          $key = isset($data['darksky_api_key']) ? $data['darksky_api_key'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return client id for ArcGIS.
   *
   * @return string
   *   The client id.
   */
  public static function getArcGisClientId() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['arcgis_client_id']) ? $data['arcgis_client_id'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['arcgis_client_id']) ? $data['arcgis_client_id'] : NULL;
          break;

        default:
          $key = isset($data['arcgis_client_id']) ? $data['arcgis_client_id'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return client secret for ArcGIS.
   *
   * @return string
   *   The client secret.
   */
  public static function getArcGisClientSecret() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['arcgis_client_secret']) ? $data['arcgis_client_secret'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['arcgis_client_secret']) ? $data['arcgis_client_secret'] : NULL;
          break;

        default:
          $key = isset($data['arcgis_client_secret']) ? $data['arcgis_client_secret'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return API key for Mailchimp.
   *
   * @return string
   *   The API key.
   */
  public static function getMailchimpApiKey() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['mailchimp_api_key']) ? $data['mailchimp_api_key'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['mailchimp_api_key']) ? $data['mailchimp_api_key'] : NULL;
          break;

        default:
          $key = isset($data['mailchimp_api_key']) ? $data['mailchimp_api_key'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return list id for Mailchimp.
   *
   * @return string
   *   The list id.
   */
  public static function getMailchimpListId() {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['mailchimp_list_id']) ? $data['mailchimp_list_id'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['mailchimp_list_id']) ? $data['mailchimp_list_id'] : NULL;
          break;

        default:
          $key = isset($data['mailchimp_list_id']) ? $data['mailchimp_list_id'] : NULL;
          break;

      }
    }
    return $key;
  }

  /**
   * Return mapbox api key.
   *
   * @return string
   *   The api key.
   */
  public static function getMapboxApiKey()
  {
    $key = NULL;
    if (defined('PANTHEON_ENVIRONMENT')) {
      $data = self::getSettings();
      switch (PANTHEON_ENVIRONMENT) {
        case self::ENVIRONMENT_LIVE:
          $key = isset($data['mapbox_api_key']) ? $data['mapbox_api_key'] : NULL;
          break;

        case self::ENVIRONMENT_TEST:
          $key = isset($data['mapbox_api_key']) ? $data['mapbox_api_key'] : NULL;
          break;

        default:
          $key = isset($data['mapbox_api_key']) ? $data['mapbox_api_key'] : NULL;
          break;

      }
    }
    return $key;
  }

}
