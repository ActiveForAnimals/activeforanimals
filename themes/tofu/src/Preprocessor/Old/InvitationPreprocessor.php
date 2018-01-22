<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\ImageController;

/**
 * Preprocessor for Invitation.
 */
class InvitationPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch elements.
    $element_controller = new ElementController();
    $image_controller = new ImageController();
    foreach ($this->variables['elements']['#storage']['invitations'] as $invitation) {
      $this->variables['content']['invitations'][] = $invitation;
    }
    return $this->variables;
  }

}
