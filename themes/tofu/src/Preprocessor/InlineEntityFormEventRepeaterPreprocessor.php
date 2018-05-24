<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Entity\ResultType;

/**
 * Preprocessor for InlineEntityFormResult.
 */
class InlineEntityFormEventRepeaterPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['step'] = $this->wrapFormElement($this->variables['form']['step'], 'step');
    $this->variables['form']['frequency'] = $this->wrapFormElement($this->variables['form']['frequency'], 'frequency');
    $this->variables['form']['end_on_date'] = $this->wrapFormElement($this->variables['form']['end_on_date'], 'end_on_date');
    return $this->variables;
  }

}
