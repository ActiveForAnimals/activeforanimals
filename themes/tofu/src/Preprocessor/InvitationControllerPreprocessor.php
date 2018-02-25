<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for InvitationController.
 */
class InvitationControllerPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    foreach ($this->variables['elements']['#storage']['invitations'] as $invitation) {
      $this->variables['content']['invitations'][] = $invitation;
    }
    return $this->variables;
  }

}
