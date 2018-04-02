<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for ImportForm.
 */
class ImportFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    switch ($this->variables['form']['#form_id']) {
      case 'import_csv_add_form':
      case 'import_csv_add_form':
        $this->variables['type'] = 'csv';
        $this->variables['form']['field_file_csv'] = $this->wrapFormElement($this->variables['form']['field_file_csv'], 'file');
        break;

    }
    $this->variables['help_button'] = [
      '#id' => 'activeforanimals_help',
      '#type' => 'button',
      '#value' => '',
      '#attributes' => [
        'title' => t('Help'),
      ],
    ];
    return $this->variables;
  }

}
