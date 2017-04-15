<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Element\FieldController;

class ImportFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    switch ($this->variables['form']['#form_id']) {
      case 'import_csv_add_form':
      case 'import_csv_add_form':
        $this->variables['type'] = 'csv';
        $this->variables['form']['field_file_csv'] = $field_controller->form($form['field_file_csv'], 'file');
        $this->variables['form']['instructions'] = $field_controller->form($form['instructions'], 'instructions');
        break;

    }
    $this->variables['form']['parent'] = $field_controller->form($form['parent'], 'parent');
    $this->variables['form']['timezone_notice'] = $field_controller->form($form['timezone_notice'], 'timezone');
    return $this->variables;
  }
}
