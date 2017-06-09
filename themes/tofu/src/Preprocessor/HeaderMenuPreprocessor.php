<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\tofu\Constant;
use Drupal\activeforanimals\Controller\ProfileBarController;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\user\Form\UserLoginForm;

/**
 * Preprocessor for HeaderMenu.
 */
class HeaderMenuPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch elements.
    $element_controller = new ElementController();
    $image_controller = new ImageController();
    $button_controller = new ButtonController();
    $logo_path = sprintf('%s/images/logo.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $front_page_link = new Url('activeforanimals.frontpage');
    $organization_overview_link = new Url('activeforanimals.organization.overview');
    $this->variables['content']['logo'] = $image_controller->view($logo_path, 'logo', NULL, $front_page_link);
    $this->variables['content']['guide'] = $element_controller->view(t('Guide'), 'guide');
    $this->variables['content']['organizations'] = $element_controller->view(t('Organizations'), 'organizations', $organization_overview_link);
    $this->variables['content']['help'] = $element_controller->view(t('Help'), 'help');
    $this->variables['content']['login'] = $button_controller->view(t('Log in'), 'login', new Url('user.login'));
    $profile_bar_controller = new ProfileBarController();
    $this->variables['content']['profile_bar'] = $profile_bar_controller->content();
    return $this->variables;
  }

}
