<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ExportForm.
 */
class ExportFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    switch ($this->variables['form']['#form_id']) {
      case 'export_csv_add_form':
      case 'export_csv_add_form':
        $this->variables['type'] = 'csv';
        $this->variables['form']['field_file_csv'] = $field_controller->form($form['field_file_csv'], 'file');
        break;

    }
    $this->variables['form']['parent'] = $field_controller->form($form['parent'], 'parent');
    return $this->variables;
  }

}
