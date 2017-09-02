<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\tofu\Constant;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\ImageController;

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
    $guide_link = new Url('activeforanimals.guide');
    $organization_overview_link = new Url('activeforanimals.organization.overview');
    $help_link = new Url('activeforanimals.help');
    $this->variables['content']['logo'] = $image_controller->view($logo_path, 'logo', NULL, $front_page_link);
    $this->variables['content']['guide'] = $element_controller->view(t('Guide'), 'guide', $guide_link);
    $this->variables['content']['organizations'] = $element_controller->view(t('Organizations'), 'organizations', $organization_overview_link);
    $this->variables['content']['help'] = $element_controller->view(t('Help'), 'help', $help_link);
    $this->variables['content']['register_link'] = $button_controller->view(t('Register'), 'register_link', new Url('user.register'));
    $this->variables['content']['login_link'] = $button_controller->view(t('Log in'), 'login_link', new Url('user.login'));
    $this->variables['content']['profile_link'] = $element_controller->view(t('Account'), 'profile_link', new Url('user.page'));
    $this->variables['content']['logout_link'] = $button_controller->view(t('Log out'), 'logout_link', new Url('user.logout.http'));
    return $this->variables;
  }

}
