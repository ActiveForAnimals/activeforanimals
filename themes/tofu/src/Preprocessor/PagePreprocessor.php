<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\InvitationController;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Page.
 */
class PagePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['content']['logo'] = $this->wrapImage(sprintf('%s/images/logo.png', drupal_get_path('theme', Constant::MACHINE_NAME)), 'logo', NULL, new Url('activeforanimals.frontpage'));
    $this->variables['content']['guide'] = $this->wrapElement(t('Guide'), 'guide', new Url('activeforanimals.guide'));
    $this->variables['content']['organizations'] = $this->wrapElement(t('Organizations'), 'organizations', new Url('entity.organization.collection'));
    $this->variables['content']['help'] = $this->wrapElement(t('Help'), 'help', new Url('activeforanimals.help'));
    $this->variables['content']['register_link'] = $this->wrapButton(t('Register'), 'register_link', new Url('user.register'));
    $this->variables['content']['login_link'] = $this->wrapButton(t('Log in'), 'login_link', new Url('user.login'));
    $this->variables['content']['profile_link'] = $this->wrapElement(t('Account'), 'profile_link', new Url('user.page'));
    $this->variables['content']['logout_link'] = $this->wrapButton(t('Log out'), 'logout_link', new Url('user.logout.http'));
    $this->variables['invitations'] = (new InvitationController())->view();
    // Add external site links.
    $this->variables['footer']['facebook_logo'] = $this->wrapImage(
      sprintf('%s/images/facebook.png', drupal_get_path('theme', Constant::MACHINE_NAME)),
      'facebook_logo',
      NULL,
      Url::fromUri('https://www.facebook.com/activeforanimals')
    );
    $this->variables['footer']['facebook_link'] = $this->wrapElement(t('Facebook'), 'facebook_link', Url::fromUri('https://www.facebook.com/activeforanimals'));
    $this->variables['footer']['mailchimp_logo'] = $this->wrapImage(
      sprintf('%s/images/mailchimp.png', drupal_get_path('theme', Constant::MACHINE_NAME)),
      'mailchimp_logo',
      NULL,
      Url::fromRoute('activeforanimals.newsletter.sign_up')
    );
    $this->variables['footer']['mailchimp_link'] = $this->wrapElement(t('Newsletter sign up'), 'mailchimp_link', Url::fromRoute('activeforanimals.newsletter.sign_up'));
    $this->variables['footer']['email_address'] = $this->wrapElement('info (at) activeforanimals.com', 'email', Url::fromUri('mailto: info (at) activeforanimals.com'));
    $this->variables['footer']['feedback'] = $this->wrapElement('Give us feedback', 'link', Url::fromRoute('contact.site_page'));
    $this->variables['footer']['afa_message'] = $this->wrapElement('Active for Animals 2016', 'link', Url::fromRoute('activeforanimals.frontpage'));
    $this->variables['footer']['darksky_message'] = $this->wrapElement('Powered by Dark Sky', 'link', Url::fromUri('https://darksky.net/poweredby/'));
    // Add theme path to drupalSettings.
    $this->variables['#attached']['drupalSettings'] = [
      'tofu' => [
        'path' => sprintf('/%s', drupal_get_path('theme', 'tofu')),
      ],
    ];
    return $this->variables;
  }

}
