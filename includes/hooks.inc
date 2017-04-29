<?php

/**
 * @file
 * Contains profile hook implements.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\activeforanimals\Hook\EntityInsertHook;

/**
 * Implements hook_entity_insert().
 */
function activeforanimals_entity_insert(EntityInterface $entity) {
  // Ensure that class is present. This is necessary because the autoloader
  // is blind on installation of the profile.
  if (class_exists('Drupal\activeforanimals\Hook\EntityInsertHook')) {
    $hook = EntityInsertHook::getInstance();
    $hook->invoke([
      'entity' => $entity,
    ]);
  }
}
