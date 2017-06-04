<?php

namespace Drupal\tofu\Hook;

use Drupal\tofu\Constant;

/**
 * Implements hook_theme().
 */
class ThemeHook implements HookInterface {

  /**
   * An instance of this class.
   *
   * @var HookImplementation
   */
  private static $instance;

  /**
   * {@inheritdoc}
   */
  public static function getInstance() {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * {@inheritdoc}
   */
  public function invoke(array $args) {
    foreach (Constant::CONTROLLER_TEMPLATES as $element) {
      $theme[$element] = $this->getElements($element);
    }
    foreach (Constant::FORM_TEMPLATES as $form_template) {
      $theme[sprintf('%s-form', $form_template)] = $this->getForm(str_replace('_', '-', $form_template));
    }
    // Update import forms.
    $theme['import-form'] = $this->getForm('import');
    // Update html element.
    $theme['html']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'html');
    $theme['html']['template'] = 'html';
    // Update page element.
    $theme['page']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'page');
    $theme['page']['template'] = 'page';
    // Update user login form.
    $theme['user_login-form']['render element'] = 'form';
    $theme['user_login-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'user_login_form');
    $theme['user_login-form']['template'] = 'user_login_form';
    // Update user password reset form.
    $theme['user_pass-form']['render element'] = 'form';
    $theme['user_pass-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'user_pass_form');
    $theme['user_pass-form']['template'] = 'user_pass_form';
    // Update organization publish form.
    $theme['publish_organization-form']['render element'] = 'form';
    $theme['publish_organization-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'publish_organization');
    $theme['publish_organization-form']['template'] = 'publish_organization';
    // Update group publish form.
    $theme['publish_group-form']['render element'] = 'form';
    $theme['publish_group-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'publish_group');
    $theme['publish_group-form']['template'] = 'publish_group';
    // Update import publish form.
    $theme['publish_import-form']['render element'] = 'form';
    $theme['publish_import-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'publish_import');
    $theme['publish_import-form']['template'] = 'publish_import';
    // Update event publish form.
    $theme['publish_event-form']['render element'] = 'form';
    $theme['publish_event-form']['path'] = sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), 'publish_event');
    $theme['publish_event-form']['template'] = 'publish_event';
    return $theme;
  }

  /**
   * Returns an entity theming item.
   *
   * @param string $element
   *   The name of the render element to return theming information for.
   *
   * @return array
   *   The theme item.
   */
  private function getElements($element) {
    return [
      'render element' => 'elements',
      'path' => sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), $element),
      'template' => $element,
    ];
  }

  /**
   * Returns an entity form theming item.
   *
   * @param string $form
   *   The name of the form to return theming information for.
   *
   * @return array
   *   The theme item.
   */
  private function getForm($form) {
    return [
      'render element' => 'form',
      'path' => sprintf('%s/templates/%s', drupal_get_path('theme', 'tofu'), $form),
      'template' => sprintf('%s-form', $form),
    ];
  }

}
