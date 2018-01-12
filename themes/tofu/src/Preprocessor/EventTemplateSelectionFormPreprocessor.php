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
    $form = $this->variables['form'];
    // Wrap elements.
    $field_controller = new FieldController();
    $this->variables['form']['organization'] = $field_controller->form($form['organization'], 'organization');
    $this->variables['form']['event_template'] = $field_controller->form($form['event_template'], 'event_template_options');
    return $this->variables;
  }

}
