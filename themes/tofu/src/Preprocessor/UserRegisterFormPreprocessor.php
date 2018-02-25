<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\afa_agarvenectarjar\Constant as AfaAlgarveNectarJarConstant;

/**
 * Preprocessor for RegisterForm.
 */
class UserRegisterFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['form']['account']['name']['#attributes']['placeholder'] = t('Username');
    $this->variables['form']['account']['mail']['#attributes']['placeholder'] = t('Email address');
    $this->variables['form']['account']['pass']['pass1']['#placeholder'] = t('Password');
    $this->variables['form']['account']['pass']['pass2']['#placeholder'] = t('Re-type password');
    $this->variables['form']['account']['name'] = $this->wrapFormElement($this->variables['form']['account']['name'], 'username');
    $this->variables['form']['account']['mail'] = $this->wrapFormElement($this->variables['form']['account']['mail'], 'email_address');
    $this->variables['form']['account']['pass'] = $this->wrapFormElement($this->variables['form']['account']['pass'], 'password');
    $this->variables['form'][AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME] = $this->wrapFormElement($this->variables['form'][AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME], AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME);
    $this->variables['form'][AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME] = $this->wrapFormElement($this->variables['form'][AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME], AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME);
    $this->variables['form']['actions']['submit'] = $this->wrapFormElement($this->variables['form']['actions']['submit'], 'submit');
    return $this->variables;
  }

}
