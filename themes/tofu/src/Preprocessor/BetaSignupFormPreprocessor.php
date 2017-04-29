<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for BetaSignupForm.
 */
class BetaSignupFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $elements = $this->variables['elements'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['elements']['email_address'] = $field_controller->form($elements['email_address'], 'email_address');
    $this->variables['elements']['name'] = $field_controller->form($elements['name'], 'name');
    $this->variables['elements']['your_organization'] = $field_controller->form($elements['your_organization'], 'your_organization');
    $this->variables['elements']['is_staff'] = $field_controller->form($elements['is_staff'], 'is_staff');
    $this->variables['elements']['message'] = $field_controller->form($elements['message'], 'message');
    $this->variables['elements']['submit'] = $field_controller->form($elements['submit'], 'button');
    $this->variables['content']['signup_message'] = [
      '#type' => 'markup',
      '#markup' => 'Sign up for beta access here. You can also join our Facebook page to keep getting updates and news on <a target="_blank" href="https://facebook.com/activeforanimals">Active for Animals on Facebook</a>.<br>',
    ];
    $this->variables['content']['join_message'] = [
      '#type' => 'markup',
      '#markup' => 'Join our Facebook page to keep getting updates and news on <a target="_blank" href="https://facebook.com/activeforanimals">Active for Animals on Facebook</a>.<br>',
    ];
    return $this->variables;
  }

}
