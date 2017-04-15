<?php

namespace Drupal\activeforanimals\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for the profile bar.
 */
class ProfileBarController extends ControllerBase {

  const THEME_ID = 'profile_bar';

  const CACHE_MAX_AGE = Cache::PERMANENT;

  const CACHE_TAGS = [
    'user',
  ];

  /**
   * Returns a render array.
   *
   * @return array
   *   A render array.
   */
  public function content() {
    $content['#theme'] = self::THEME_ID;
    $content['#cache'] = [
      'max-age' => self::CACHE_MAX_AGE,
      'tags' => self::CACHE_TAGS,
    ];
    return $content;
  }
}
