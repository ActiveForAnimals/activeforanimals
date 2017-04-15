<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ElementController;

class UserFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $form['account']['current_pass']['#placeholder'] = t('Current password');
    $form['account']['mail']['#placeholder'] = t('E-mail address');
    $form['account']['pass']['pass1']['#placeholder'] = t('New password');
    $form['account']['pass']['pass2']['#placeholder'] = t('Re-type new password');
    $this->variables['form']['account']['pass'] = $field_controller->form($form['account']['pass'], 'password');
    $this->variables['form']['account']['mail'] = $field_controller->form($form['account']['mail'], 'email_address');
    $this->variables['form']['account']['current_pass'] = $field_controller->form($form['account']['current_pass'], 'password');
    return $this->variables;
  }
}
