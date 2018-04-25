<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for EventTemplateForm.
 */
class EventTemplateFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['name'] = $this->wrapFormElement($this->variables['form']['name'], 'title');
    $this->variables['form']['event_title'] = $this->wrapFormElement($this->variables['form']['event_title'], 'title');
    $this->variables['form']['event_start_date'] = $this->wrapFormElement($this->variables['form']['event_start_date'], 'start_date');
    $this->variables['form']['event_end_date'] = $this->wrapFormElement($this->variables['form']['event_end_date'], 'end_date');
    $this->variables['form']['event_description'] = $this->wrapFormElement($this->variables['form']['event_description'], 'description');
    $this->variables['form']['event_location'] = $this->wrapFormElement($this->variables['form']['event_location'], 'location');
    return $this->variables;
  }

}
