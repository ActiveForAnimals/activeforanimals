<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ElementController;

class UserPasswordFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    return $this->variables;
  }
}
