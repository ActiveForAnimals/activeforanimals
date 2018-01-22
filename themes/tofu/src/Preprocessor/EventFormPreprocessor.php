<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for EventForm.
 */
class EventFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['title'] = $this->wrapFormElement($this->variables['form']['title'], 'title');
    $this->variables['form']['parent'] = $this->wrapFormElement($this->variables['form']['parent'], 'parent');
    $this->variables['form']['description'] = $this->wrapFormElement($this->variables['form']['description'], 'description');
    $this->variables['form']['location'] = $this->wrapFormElement($this->variables['form']['location'], 'location');
    $this->variables['form']['start_date'] = $this->wrapFormElement($this->variables['form']['start_date'], 'start_date');
    $this->variables['form']['end_date'] = $this->wrapFormElement($this->variables['form']['end_date'], 'end_date');
    $this->variables['form']['results'] = $this->wrapFormElement($this->variables['form']['results'], 'inline_entity_form');
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
