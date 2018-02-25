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
    $this->variables['form']['event_description'] = $this->wrapFormElement($this->variables['form']['event_description'], 'description');
    return $this->variables;
  }

}
