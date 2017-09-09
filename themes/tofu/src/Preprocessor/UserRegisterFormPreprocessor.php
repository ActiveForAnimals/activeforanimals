<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\afa_agarvenectarjar\Constant as AfaAlgarveNectarJarConstant;

/**
 * Preprocessor for UserLoginForm.
 */
class UserRegisterFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $form['account']['name']['#attributes']['placeholder'] = t('Username');
    $form['account']['mail']['#attributes']['placeholder'] = t('Email address');
    $form['account']['pass']['pass1']['#placeholder'] = t('Password');
    $form['account']['pass']['pass2']['#placeholder'] = t('Re-type password');
    $this->variables['form']['account']['name'] = $field_controller->form($form['account']['name'], 'username');
    $this->variables['form']['account']['mail'] = $field_controller->form($form['account']['mail'], 'email_address');
    $this->variables['form']['account']['pass'] = $field_controller->form($form['account']['pass'], 'password');
    $this->variables['form']['account'][AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME] = $field_controller->form($form['account'][AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME], AfaAlgarveNectarJarConstant::SHOWN_FORM_ELEMENT_MACHINE_NAME);
    $this->variables['form']['account'][AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME] = $field_controller->form($form['account'][AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME], AfaAlgarveNectarJarConstant::HIDDEN_FORM_ELEMENT_MACHINE_NAME);
    $this->variables['form']['actions']['submit'] = $field_controller->form($form['actions']['submit'], 'submit');
    return $this->variables;
  }

}
