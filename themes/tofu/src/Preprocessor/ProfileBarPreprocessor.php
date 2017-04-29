<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ProfileBar.
 */
class ProfileBarPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $user_account_link = new Url('user.page');
    $logout_link = new Url('user.logout.http');
    $this->variables['content']['profile_link'] = $element_controller->view(t('Account'), 'profile_link', $user_account_link);
    $this->variables['content']['logout_link'] = $button_controller->view(t('Log out'), 'logout_link', $logout_link);
    return $this->variables;
  }

}
