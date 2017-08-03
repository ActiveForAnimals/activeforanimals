<?php

namespace Drupal\activeforanimals\Helper;

use Drupal;
use Drupal\Core\Entity\EntityInterface;
use Drupal\effective_activism\Constant as EffectiveActivismConstant;

/**
 * Helper functions for path aliases.
 */
class PathAliasHelper {

  const PATH_TEMPLATE = [
    EffectiveActivismConstant::ENTITY_ORGANIZATION => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
      'groups' => 'g',
    ],
    EffectiveActivismConstant::ENTITY_GROUP => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
      'imports' => 'i',
      'events' => 'e',
      'results' => 'r',
    ],
    EffectiveActivismConstant::ENTITY_IMPORT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_EVENT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_RESULT_TYPE => [
      'edit' => 'edit',
    ],
  ];

  /**
   * Adds path aliases for entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   */
  public static function add(EntityInterface $entity) {
    if (in_array($entity->getEntityType()->id(), array_keys(self::PATH_TEMPLATE))) {
      // Determine system path and any parent entity paths.
      $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
      $path = NULL;
      switch ($entity->getEntityType()->id()) {
        case EffectiveActivismConstant::ENTITY_ORGANIZATION:
          $path = self::ensureUniquePath(sprintf('/o/%s', self::getSlug($entity->label())));
          self::connect('/manage/groups/add', sprintf('%s/%s', $path, 'add-group'));
          break;

        case EffectiveActivismConstant::ENTITY_GROUP:
          $path = self::ensureUniquePath(sprintf('%s/g/%s', self::get($entity->get('organization')->entity), self::getSlug($entity->label())));
          self::connect('/manage/events/add', sprintf('%s/%s', $path, 'add-event'));
          self::connect('/manage/imports/add', sprintf('%s/%s', $path, 'add-import'));
          break;

        case EffectiveActivismConstant::ENTITY_IMPORT:
          $path = self::ensureUniquePath(sprintf('%s/i/%d', self::get($entity->get('parent')->entity), self::getSlug($entity->label())));
          break;

        case EffectiveActivismConstant::ENTITY_EVENT:
          $path = self::ensureUniquePath(sprintf('%s/e/%d', self::get($entity->get('parent')->entity), self::getSlug($entity->label())));
          break;

        case EffectiveActivismConstant::ENTITY_RESULT_TYPE:
          $path = self::ensureUniquePath(sprintf('%s/t/%s', self::get($entity->get('parent')->entity), self::getSlug($entity->label())));
          break;
      }
      // Apply path template to populate entity aliases.
      if (!empty($path)) {
        foreach (self::PATH_TEMPLATE[$entity->getEntityType()->id()] as $system_slug => $alias) {
          $system_path_format = empty($system_slug) ? '%s' : '%s/%s';
          $alias_path_format = empty($alias) ? '%s' : '%s/%s';
          self::connect(sprintf($system_path_format, $system_path, $system_slug), sprintf($alias_path_format, $path, $alias));
        }
      }
    }
  }

  /**
   * Adds path aliases for entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   */
  public static function update(EntityInterface $entity) {
    if (in_array($entity->getEntityType()->id(), array_keys(self::PATH_TEMPLATE))) {
      // Apply path template to populate missing entity aliases.
      $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
      $path = $entity->toUrl()->toString();
      // If entity doesn't have a path alias, create a new one.
      if ($system_path === $path) {
        
      }
      if (!empty($path)) {
        foreach (self::PATH_TEMPLATE[$entity->getEntityType()->id()] as $system_slug => $alias) {
          $system_path_format = empty($system_slug) ? '%s' : '%s/%s';
          $alias_path_format = empty($alias) ? '%s' : '%s/%s';
          if (!self::checkAliasExists(sprintf($alias_path_format, $path, $alias))) {
            self::connect(sprintf($system_path_format, $system_path, $system_slug), sprintf($alias_path_format, $path, $alias));
          }
        }
      }
    }
  }

  /**
   * Returns the path alias for an entity and link type.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The path to process.
   * @param string $rel
   *   The link relationship type, for example: canonical or edit-form.
   *
   * @return string|bool
   *   Returns a unique path or FALSE if entity is not recognized.
   */
  public static function get(EntityInterface $entity, $rel = 'canonical') {
    return Drupal::service('path.alias_manager')->getAliasByPath(sprintf('/%s', $entity->toUrl($rel)->getInternalPath()));
  }

  /**
   * Returns an ascii-friendly slug from a text string.
   *
   * @param string $text
   *   The text to transform into a path.
   *
   * @return string|bool
   *   Returns a slug based on the text.
   */
  private static function getSlug($text) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $slug = strtolower($slug);
    $slug = str_replace(" ", "-", $slug);
    $slug = preg_replace("/[^a-z0-9\-]/", '', $slug);
    $slug = substr($slug, 0, 30);
    return $slug;
  }

  /**
   * Checks if path alias exists.
   *
   * @param string $path
   *   The path to process.
   *
   * @return bool
   *   Returns TRUE if path exists, FALSE otherwise.
   */
  private static function checkAliasExists($path) {
    $path = Drupal::service('path.alias_storage')->load(['alias' => $path]); 
    return $path === FALSE ? FALSE : TRUE;
  }

  /**
   * Returns a unique path based on a path.
   *
   * @param string $path
   *   The path to process.
   *
   * @return string
   *   Returns a unique path.
   */
  private static function ensureUniquePath($path) {
    $variant = 0;
    $original_path = $path;
    // Check if a variant path already exists.
    while (self::checkAliasExists($path) !== FALSE) {
      $variant += 1;
      $path = substr($original_path, 0, 30 - (count($variant) + 1)) . sprintf('-%d', $variant);
    }
    return $path;
  }

  /**
   * Connect all paths required for the entity.
   *
   * @param string $system_path
   *   The system path of the entity.
   * @param string $path
   *   The path to add.
   */
  private static function connect($system_path, $path) {
    if (self::checkAliasExists($path) === FALSE) {
      Drupal::service('path.alias_storage')->save($system_path, $path);
    }
  }

}
