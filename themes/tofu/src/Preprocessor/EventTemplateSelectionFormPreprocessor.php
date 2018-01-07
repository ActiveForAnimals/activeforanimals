<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventTemplateSelectionForm.
 */
class EventTemplateSelectionFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['elements']['form'];
    // Fetch elements.
    $field_controller = new FieldController();
    $this->variables['elements']['form']['organization'] = $field_controller->form($form['organization'], 'organization');
    $this->variables['elements']['form']['event_template'] = $field_controller->form($form['event_template'], 'event_template_options');
    return $this->variables;
  }

}
