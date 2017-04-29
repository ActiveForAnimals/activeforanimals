<?php

/**
 * @file
 * Contains theme hook implements.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\tofu\Hook\FormAlterHook;
use Drupal\tofu\Hook\PreprocessHook;
use Drupal\tofu\Hook\ThemeHook;

/**
 * Implements hook_form_alter().
 */
function tofu_form_alter(array &$form, FormStateInterface $form_state, $form_id) {
  $hook = FormAlterHook::getInstance();
  $args = [
    'form' => $form,
    'form_state' => $form_state,
    'form_id' => $form_id,
  ];
  $form = $hook->invoke($args);
}

/**
 * Implements hook_preprocess().
 */
function tofu_preprocess(array &$variables, $hook_name) {
  $hook = PreprocessHook::getInstance();
  $args = [
    'variables' => $variables,
    'hook' => $hook_name,
  ];
  $variables = $hook->invoke($args);
}

/**
 * Implements hook_theme().
 */
function tofu_theme(array $existing, $type, $theme, $path) {
  $hook = ThemeHook::getInstance();
  $args = [
    'existing' => $existing,
    'type' => $type,
    'theme' => $theme,
    'path' => $path,
  ];
  return $hook->invoke($args);
}
