<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for ExportForm.
 */
class ExportFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    switch ($this->variables['form']['#form_id']) {
      case 'export_csv_add_form':
        $this->variables['type'] = 'csv';
        $this->variables['form']['field_file_csv'] = $this->wrapFormElement($this->variables['form']['field_file_csv'], 'file');
        $this->variables['form']['filter'] = $this->wrapFormElement($this->variables['form']['filter'], 'filter');
        $this->variables['form']['columns_event'] = $this->wrapFormElement($this->variables['form']['columns_event'], 'columns');
        $this->variables['form']['columns_result'] = $this->wrapFormElement($this->variables['form']['columns_result'], 'columns');
        $this->variables['form']['columns_third_party_content'] = $this->wrapFormElement($this->variables['form']['columns_third_party_content'], 'columns');
        break;

    }
    return $this->variables;
  }

}
