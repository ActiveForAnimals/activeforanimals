<?php

namespace Drupal\activeforanimals\Controller;

use Drupal\activeforanimals\ListBuilder\UserEventsListBuilder;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use ReflectionClass;

/**
 * Renders a list of events created by current user.
 *
 * @ingroup effective_activism
 */
class MyEventsController extends ControllerBase {

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
    $my_events = new UserEventsListBuilder(
      $this->entityTypeManager()->getDefinition('event'),
      $this->entityTypeManager()->getStorage('event'),
      $this->currentUser()
    );
    $content['my_events'] = $my_events->render();
    return $content;
  }

}
