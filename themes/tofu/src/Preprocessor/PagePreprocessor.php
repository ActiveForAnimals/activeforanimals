<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Misc\HeaderMenuController;
use Drupal\effective_activism\Controller\Misc\InvitationController;

/**
 * Preprocessor for Page.
 */
class PagePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['header_menu'] = (new HeaderMenuController())->view();
    $this->variables['invitations'] = (new InvitationController())->view();
    // Add theme path to drupalSettings.
    $this->variables['#attached']['drupalSettings'] = [
      'tofu' => [
        'path' => sprintf('/%s', drupal_get_path('theme', 'tofu')),
      ],
    ];
    return $this->variables;
  }

}
