<?php

use Drupal\Component\Utility\Html;
use Drupal\tofu\Constant;

/**
 * Preprocess variables for html templates.
 *
 * @param array $variables
 *   An associative array.
 */
function tofu_preprocess_html(&$variables) {
  foreach (Constant::ENTITY_TYPES as $entity_type) {
    $entity = \Drupal::routeMatch()->getParameter($entity_type);
    if ($entity !== FALSE) {
      $variables['entity_type'] = $entity_type;
      $current_path = \Drupal::service('path.current')->getPath();
      $pieces = explode('/', $current_path);
      if (in_array(end($pieces), Constant::MANAGEMENT_SUBPATH)) {
        $variables['editing'] = TRUE;
      }
      break;
    }
  }
}
