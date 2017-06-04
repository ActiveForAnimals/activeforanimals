<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\tofu\Constant;

/**
 * Preprocessor for HTML.
 */
class HtmlPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    foreach (Constant::ENTITY_TYPES as $entity_type) {
      $entity = Drupal::routeMatch()->getParameter($entity_type);
      if ($entity !== FALSE) {
        $this->variables['entity_type'] = $entity_type;
        $current_path = \Drupal::service('path.current')->getPath();
        $pieces = explode('/', $current_path);
        if (in_array(end($pieces), Constant::MANAGEMENT_SUBPATH)) {
          $this->variables['editing'] = TRUE;
        }
        break;
      }
    }
    return $this->variables;
  }

}
