<?php

namespace Drupal\activeforanimals\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use ReflectionClass;

/**
 * Controller class for static pages.
 */
class StaticPageController extends ControllerBase {

  const CACHE_MAX_AGE = Cache::PERMANENT;

  const CACHE_TAGS = [];

  /**
   * Returns a render array.
   *
   * @param string $filepath
   *   The path to the static content.
   * @param string $filename
   *   The file name of the static content.
   *
   * @return array
   *   A render array.
   */
  public function content($filepath = NULL, $filename = NULL) {
    $content['#filepath'] = $filepath;
    $content['#filename'] = $filename;
    $content['#imagepath'] = sprintf('/%s/images', drupal_get_path('profile', 'activeforanimals'));
    $content['#theme'] = (new ReflectionClass($this))->getShortName();
    $content['#cache'] = [
      'max-age' => self::CACHE_MAX_AGE,
      'tags' => self::CACHE_TAGS,
    ];
    return $content;
  }

}
