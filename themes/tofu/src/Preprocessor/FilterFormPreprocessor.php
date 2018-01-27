<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for FilterForm.
 */
class FilterFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['name'] = $field_controller->form($form['name'], 'title');
    $this->variables['form']['start_date'] = $field_controller->form($form['start_date'], 'date');
    $this->variables['form']['end_date'] = $field_controller->form($form['end_date'], 'date');
    return $this->variables;
  }

}
