<?php

/**
 * @file
 * Contains profile hook implements.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\activeforanimals\Hook\EntityInsertHook;
use Drupal\activeforanimals\Hook\EntityUpdateHook;

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

/**
 * Implements hook_entity_update().
 */
function activeforanimals_entity_update(EntityInterface $entity) {
  // Ensure that class is present. This is necessary because the autoloader
  // is blind on installation of the profile.
  if (class_exists('Drupal\activeforanimals\Hook\EntityUpdateHook')) {
    $hook = EntityUpdateHook::getInstance();
    $hook->invoke([
      'entity' => $entity,
    ]);
  }
}
