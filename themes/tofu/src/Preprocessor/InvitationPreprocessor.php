<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\tofu\Constant;
use Drupal\activeforanimals\Controller\ProfileBarController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\tofu\Preprocessor\PreprocessorInterface;

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
