<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventForm.
 */
class EventFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['title'] = $field_controller->form($form['title'], 'title');
    $this->variables['form']['parent'] = $field_controller->form($form['parent'], 'parent');
    $this->variables['form']['description'] = $field_controller->form($form['description'], 'description');
    $this->variables['form']['location'] = $field_controller->form($form['location'], 'location');
    $this->variables['form']['start_date'] = $field_controller->form($form['start_date'], 'start_date');
    $this->variables['form']['end_date'] = $field_controller->form($form['end_date'], 'end_date');
    $this->variables['form']['results'] = $field_controller->form($form['results'], 'inline_entity_form');
    return $this->variables;
  }

}
