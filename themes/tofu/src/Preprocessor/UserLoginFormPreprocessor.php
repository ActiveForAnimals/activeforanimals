<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for UserLoginForm.
 */
class UserLoginFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['name']['#attributes']['placeholder'] = t('Username');
    $this->variables['form']['pass']['#attributes']['placeholder'] = t('Password');
    $this->variables['form']['name'] = $this->wrapFormElement($this->variables['form']['name'], 'name');
    $this->variables['form']['pass'] = $this->wrapFormElement($this->variables['form']['pass'], 'pass');
    $this->variables['form']['actions']['submit'] = $this->wrapFormElement($this->variables['form']['actions']['submit'], 'submit');
    return $this->variables;
  }

}
