<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for UserForm.
 */
class UserFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['account']['current_pass']['#placeholder'] = t('Current password');
    $this->variables['form']['account']['mail']['#placeholder'] = t('E-mail address');
    $this->variables['form']['account']['pass']['pass1']['#placeholder'] = t('New password');
    $this->variables['form']['account']['pass']['pass2']['#placeholder'] = t('Re-type new password');
    $this->variables['form']['account']['pass'] = $this->wrapFormElement($this->variables['form']['account']['pass'], 'password');
    $this->variables['form']['account']['mail'] = $this->wrapFormElement($this->variables['form']['account']['mail'], 'email_address');
    $this->variables['form']['account']['current_pass'] = $this->wrapFormElement($this->variables['form']['account']['current_pass'], 'password');
    return $this->variables;
  }

}
