<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ElementController;

/**
 * Preprocessor for User.
 */
class UserPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch User Entity Object.
    $user = $this->variables['elements']['#user'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $this->variables['content']['username'] = $element_controller->view($user->getDisplayName(), 'username');
    $this->variables['content']['email_address'] = $element_controller->view($user->getEmail(), 'email_address');
    $this->variables['content']['edit_link'] = $element_controller->view(t('Edit account'), 'edit_page', new Url(
      'entity.user.edit_form', [
        'user' => $user->id(),
      ]
    ));
    return $this->variables;
  }

}
