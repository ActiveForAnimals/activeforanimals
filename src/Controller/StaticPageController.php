<?php

namespace Drupal\activeforanimals\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for static pages.
 */
class StaticPageController extends ControllerBase {

  const THEME_ID = 'static_page';

  const CACHE_MAX_AGE = Cache::PERMANENT;

  const CACHE_TAGS = [];

  /**
   * Returns a render array.
   *
   * @param string $filepath
   *   The path to the static content.
   *
   * @return array
   *   A render array.
   */
  public function content($filepath = NULL) {
    $content['#filepath'] = $filepath;
    $content['#theme'] = self::THEME_ID;
    $content['#cache'] = [
      'max-age' => self::CACHE_MAX_AGE,
      'tags' => self::CACHE_TAGS,
    ];
    return $content;
  }

}
