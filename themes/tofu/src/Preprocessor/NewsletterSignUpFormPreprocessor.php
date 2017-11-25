<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for newsletter sign-up form.
 */
class NewsletterSignUpFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['email'] = $field_controller->form($form['email'], 'email');
    $this->variables['form']['first_name'] = $field_controller->form($form['first_name'], 'name');
    $this->variables['form']['last_name'] = $field_controller->form($form['last_name'], 'name');
    return $this->variables;
  }

}
