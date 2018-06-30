<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for EventRepeaterForm.
 */
class EventRepeaterFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['step'] = $this->wrapFormElement($this->variables['form']['step'], 'step');
    $this->variables['form']['frequency'] = $this->wrapFormElement($this->variables['form']['frequency'], 'frequency');
    $this->variables['form']['repeats'] = $this->wrapFormElement($this->variables['form']['repeats'], 'repeats');
    return $this->variables;
  }

}
