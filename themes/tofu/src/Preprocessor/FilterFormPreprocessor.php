<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for FilterForm.
 */
class FilterFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['name'] = $this->wrapFormElement($this->variables['form']['name'], 'title');
    $this->variables['form']['start_date'] = $this->wrapFormElement($this->variables['form']['start_date'], 'start_date');
    $this->variables['form']['end_date'] = $this->wrapFormElement($this->variables['form']['end_date'], 'end_date');
    $this->variables['form']['event_template'] = $this->wrapFormElement($this->variables['form']['event_template'], 'event_template');
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
