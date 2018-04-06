<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for newsletter sign-up form.
 */
class NewsletterSignUpFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['email'] = $this->wrapFormElement($this->variables['form']['email'], 'email');
    $this->variables['form']['first_name'] = $this->wrapFormElement($this->variables['form']['first_name'], 'name');
    $this->variables['form']['last_name'] = $this->wrapFormElement($this->variables['form']['last_name'], 'name');
    $this->variables['content']['latest_news'] = 'xxx';
    return $this->variables;
  }

}
