<?php

namespace Drupal\activeforanimals\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use ReflectionClass;

/**
 * Controller class for the front page.
 */
class FrontPageController extends ControllerBase {

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
    $content['#theme'] = (new ReflectionClass($this))->getShortName();
    $content['#cache'] = [
      'max-age' => self::CACHE_MAX_AGE,
      'tags' => self::CACHE_TAGS,
    ];
    return $content;
  }

}
