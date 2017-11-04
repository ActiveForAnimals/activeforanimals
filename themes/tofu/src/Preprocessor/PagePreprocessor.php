<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Misc\HeaderMenuController;
use Drupal\effective_activism\Controller\Misc\InvitationController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Page.
 */
class PagePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $image_controller = new ImageController();
    $element_controller = new ElementController();
    $this->variables['header_menu'] = (new HeaderMenuController())->view();
    $this->variables['invitations'] = (new InvitationController())->view();
    // Add external site links.
    $facebook_logo_path = sprintf('%s/images/facebook.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $github_logo_path = sprintf('%s/images/github.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $this->variables['footer']['facebook_logo'] = $image_controller->view($facebook_logo_path, 'facebook_logo', NULL, Url::fromUri('https://www.facebook.com/activeforanimals'));
    $this->variables['footer']['facebook_link'] = $element_controller->view(t('Facebook'), 'facebook_link', Url::fromUri('https://www.facebook.com/activeforanimals'));
    $this->variables['footer']['email_address'] = $element_controller->view('info (at) activeforanimals.com', 'email', Url::fromUri('mailto: info (at) activeforanimals.com'));
    $this->variables['footer']['feedback'] = $element_controller->view('Give us feedback', 'link', Url::fromRoute('contact.site_page'));
    // Add theme path to drupalSettings.
    $this->variables['#attached']['drupalSettings'] = [
      'tofu' => [
        'path' => sprintf('/%s', drupal_get_path('theme', 'tofu')),
      ],
    ];
    return $this->variables;
  }

}
