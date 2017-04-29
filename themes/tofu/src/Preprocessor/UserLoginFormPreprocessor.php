<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for UserLoginForm.
 */
class UserLoginFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $form['name']['#attributes']['placeholder'] = t('Username');
    $form['pass']['#attributes']['placeholder'] = t('Password');
    $this->variables['form']['name'] = $field_controller->form($form['name'], 'name');
    $this->variables['form']['pass'] = $field_controller->form($form['pass'], 'pass');
    $this->variables['form']['actions']['submit'] = $field_controller->form($form['actions']['submit'], 'submit');
    return $this->variables;
  }

}
