<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ContactInformation.
 */
class ContactInformationPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch elements.
    $field_controller = new FieldController();
    foreach ($this->variables['elements']['fields'] as $field_name => $field) {
      // Process field objects.
      if (is_object($field) && !$field->isEmpty()) {
        $this->variables['content'][$field_name] = $field_controller->view($field);
      }
      // Process form element arrays.
      elseif (is_array($field)) {
        $this->variables['content'][$field_name] = $field_controller->form($field, $field_name);
      }
    }
    return $this->variables;
  }

}
