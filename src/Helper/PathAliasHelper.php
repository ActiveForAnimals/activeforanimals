<?php

namespace Drupal\activeforanimals\Helper;

use Drupal;
use Drupal\Core\Entity\EntityInterface;
use Drupal\effective_activism\Constant as EffectiveActivismConstant;

/**
 * Helper functions for path aliases.
 */
class PathAliasHelper {

  /**
   * Path templates for entities.
   */
  const PATH_TEMPLATE = [
    EffectiveActivismConstant::ENTITY_EVENT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_EVENT_TEMPLATE => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_EXPORT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_FILTER => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_GROUP => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
      'imports' => 'imports',
      'events' => 'e',
    ],
    EffectiveActivismConstant::ENTITY_IMPORT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_ORGANIZATION => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
      'groups' => 'g',
      'event-templates' => 'event-templates',
      'exports' => 'exports',
      'filters' => 'filters',
      'results' => 'r',
    ],
    EffectiveActivismConstant::ENTITY_RESULT_TYPE => [
      '' => '',
    ],
  ];

}
