<?php

/**
 * @file
 * Contains profile hook implements.
 */

use Drupal\activeforanimals\Hook\EntityInsertHook;
use Drupal\activeforanimals\Hook\EntityUpdateHook;
use Drupal\activeforanimals\Hook\FormAlterHook;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
function activeforanimals_form_alter(array &$form, FormStateInterface $form_state, $form_id) {
  // Ensure that class is present. This is necessary because the autoloader
  // is blind on installation of the profile.
  if (class_exists('Drupal\activeforanimals\Hook\FormAlterHook')) {
    $hook = FormAlterHook::getInstance();
    $args = [
      'form' => $form,
      'form_state' => $form_state,
      'form_id' => $form_id,
    ];
    $form = $hook->invoke($args);
  }
}

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
