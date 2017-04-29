<?php

namespace Drupal\activeforanimals\Helper;

use Drupal\Core\Entity\EntityInterface;

/**
 * Helper functions for path aliases.
 */
class PathAliasHelper {

  /**
   * Adds path aliases for entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to process.
   */
  public static function add(EntityInterface $entity) {
    switch ($entity->getEntityType()->id()) {
      case 'organization':
        $organization_slug = self::getSlug($entity->label());
        $path = self::ensureUniquePath(sprintf('/o/%s', $organization_slug));
        $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
        self::connect($system_path, $path);
        self::connect(sprintf('%s/%s', $system_path, 'edit'), sprintf('%s/%s', $path, 'edit'));
        self::connect(sprintf('%s/%s', $system_path, 'publish'), sprintf('%s/%s', $path, 'publish'));
        self::connect(sprintf('%s/%s', $system_path, 'groups'), sprintf('%s/%s', $path, 'g'));
        self::connect('/manage/groups/add', sprintf('%s/%s', $path, 'add-group'));
        break;

      case 'group':
        $organization_path = self::get($entity->get('organization')->entity);
        $group_slug = self::getSlug($entity->label());
        $path = self::ensureUniquePath(sprintf('%s/g/%s', $organization_path, $group_slug));
        $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
        self::connect($system_path, $path);
        self::connect(sprintf('%s/%s', $system_path, 'edit'), sprintf('%s/%s', $path, 'edit'));
        self::connect(sprintf('%s/%s', $system_path, 'publish'), sprintf('%s/%s', $path, 'publish'));
        self::connect(sprintf('%s/%s', $system_path, 'imports'), sprintf('%s/%s', $path, 'i'));
        self::connect(sprintf('%s/%s', $system_path, 'events'), sprintf('%s/%s', $path, 'e'));
        self::connect('/manage/events/add', sprintf('%s/%s', $path, 'add-event'));
        self::connect('/manage/imports/add', sprintf('%s/%s', $path, 'add-import'));
        break;

      case 'import':
        $group_path = self::get($entity->get('parent')->entity);
        $path = self::ensureUniquePath(sprintf('%s/i/%d', $group_path, $entity->id()));
        $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
        self::connect($system_path, $path);
        self::connect(sprintf('%s/%s', $system_path, 'edit'), sprintf('%s/%s', $path, 'edit'));
        self::connect(sprintf('%s/%s', $system_path, 'publish'), sprintf('%s/%s', $path, 'publish'));
        break;

      case 'event':
        $group_path = self::get($entity->get('parent')->entity);
        $path = self::ensureUniquePath(sprintf('%s/e/%d', $group_path, $entity->id()));
        $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
        self::connect($system_path, $path);
        self::connect(sprintf('%s/%s', $system_path, 'edit'), sprintf('%s/%s', $path, 'edit'));
        self::connect(sprintf('%s/%s', $system_path, 'publish'), sprintf('%s/%s', $path, 'publish'));
        break;

      case 'result_type':
        $result_type_overview_path = '/result-types';
        $path = self::ensureUniquePath(sprintf('%s/t/%s', $result_type_overview_path, $entity->id()));
        $system_path = sprintf('/%s', $entity->toUrl()->getInternalPath());
        self::connect($system_path, sprintf('%s/%s', $path, 'edit'));
        break;
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
    return \Drupal::service('path.alias_manager')->getAliasByPath(sprintf('/%s', $entity->toUrl($rel)->getInternalPath()));
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
    while (\Drupal::service('path.alias_storage')->load(['alias' => $path]) !== FALSE) {
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
    \Drupal::service('path.alias_storage')->save($system_path, $path);
  }

}
