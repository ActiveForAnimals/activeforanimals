<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;

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
    $this->variables['content']['username'] = $this->wrapElement($user->getDisplayName(), 'username');
    $this->variables['content']['email_address'] = $this->wrapElement($user->getEmail(), 'email_address');
    $this->variables['content']['edit_link'] = $this->wrapElement(t('Edit account'), 'edit_page', new Url(
      'entity.user.edit_form', [
        'user' => $user->id(),
      ]
    ));
    return $this->variables;
  }

}
