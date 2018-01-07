<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventTemplateForm.
 */
class EventTemplateFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['name'] = $field_controller->form($form['name'], 'title');
    $this->variables['form']['event_title'] = $field_controller->form($form['event_title'], 'title');
    $this->variables['form']['event_description'] = $field_controller->form($form['event_description'], 'description');
    return $this->variables;
  }

}
