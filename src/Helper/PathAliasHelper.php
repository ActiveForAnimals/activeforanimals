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
    EffectiveActivismConstant::ENTITY_EXPORT => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
    ],
    EffectiveActivismConstant::ENTITY_GROUP => [
      '' => '',
      'edit' => 'edit',
      'publish' => 'publish',
      'imports' => 'imports',
      'exports' => 'exports',
      'events' => 'e',
      'results' => 'r',
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
    ],
    EffectiveActivismConstant::ENTITY_RESULT_TYPE => [
      '' => '',
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
      $alias_path = self::createPathAlias($entity);
      if ($alias_path !== FALSE) {
        self::addSlugs($entity, $system_path, $alias_path);
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
      $alias_path = $entity->toUrl()->toString();
      // If entity doesn't have a path alias, create a new one.
      if ($system_path === $alias_path) {
        self::add($entity);
      }
      // Else, add any missing slugs.
      elseif (!empty($alias_path)) {
        self::addSlugs($entity, $system_path, $alias_path);
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
   * Returns a base path alias for the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   *
   * @return string|bool
   *   The base path alias for the entity or FALSE if entity is not supported.
   */
  private static function createPathAlias(EntityInterface $entity) {
    $alias_path = FALSE;
    switch ($entity->getEntityType()->id()) {
      case EffectiveActivismConstant::ENTITY_ORGANIZATION:
        $alias_path = self::ensureUniquePath(sprintf('/o/%s', self::transliterate($entity->label())));
        break;

      case EffectiveActivismConstant::ENTITY_GROUP:
        $alias_path = self::ensureUniquePath(sprintf('%s/g/%s', self::get($entity->get('organization')->entity), self::transliterate($entity->label())));
        break;

      case EffectiveActivismConstant::ENTITY_IMPORT:
        $alias_path = self::ensureUniquePath(sprintf('%s/imports/%d', self::get($entity->get('parent')->entity), self::transliterate($entity->id())));
        break;

      case EffectiveActivismConstant::ENTITY_EXPORT:
        $alias_path = self::ensureUniquePath(sprintf('%s/exports/%d', self::get($entity->get('organization')->entity), self::transliterate($entity->id())));
        break;

      case EffectiveActivismConstant::ENTITY_EVENT:
        $alias_path = self::ensureUniquePath(sprintf('%s/e/%d', self::get($entity->get('parent')->entity), self::transliterate($entity->id())));
        break;

      case EffectiveActivismConstant::ENTITY_RESULT_TYPE:
        $alias_path = self::ensureUniquePath(sprintf('%s/t/%s/%s', 'result-types', self::transliterate($entity->id()), 'edit'));
        break;
    }
    // Make sure that there is a leading front-slash.
    if ($alias_path !== FALSE && substr($alias_path, 0, 1) !== '/') {
      $alias_path = sprintf('/%s', $alias_path);
    }
    return $alias_path;
  }

  /**
   * Adds all subpaths for an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   * @param string $system_path
   *   The system path of the entity.
   * @param string $alias_path
   *   The base path alias of the entity.
   */
  private static function addSlugs(EntityInterface $entity, $system_path, $alias_path) {
    foreach (self::PATH_TEMPLATE[$entity->getEntityType()->id()] as $system_slug => $alias_slug) {
      $system_path_format = empty($system_slug) ? '%s' : '%s/%s';
      $alias_path_format = empty($alias_slug) ? '%s' : '%s/%s';
      if (!self::checkAliasExists(sprintf($alias_path_format, $alias_path, $alias_slug))) {
        self::connect(sprintf($system_path_format, $system_path, $system_slug), sprintf($alias_path_format, $alias_path, $alias_slug));
      }
    }
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
  private static function transliterate($text) {
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
    $alias = Drupal::service('path.alias_storage')->load(['alias' => $path]);
    return $alias === FALSE ? FALSE : TRUE;
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
      $path = sprintf('%s-%d', substr($original_path, 0, 30 - (count($variant) + 1)), $variant);
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
