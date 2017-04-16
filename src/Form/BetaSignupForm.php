<?php

namespace Drupal\activeforanimals\Form;

use Drupal\activeforanimals\Settings;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\Plugin\Mail\PhpMail;
use Drupal;

/**
 * Beta sign-up form.
 *
 * @ingroup effective_activism
 */
class BetaSignupForm extends FormBase {

  const FORM_ID = 'beta_signup';
  const THEME_ID = self::FORM_ID;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return self::FORM_ID;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['email_address'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this->t('Your e-mail address'),
      '#placeholder' => $this->t('Your e-mail address'),
    ];
    $form['name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Your name'),
      '#placeholder' => $this->t('Your name'),
    ];
    $form['your_organization'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Your organization'),
      '#placeholder' => $this->t('Your organization'),
    ];
    $form['is_staff'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I am a staff member of the organization.'),
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#title' => $this->t('Optional message'),
      '#placeholder' => $this->t('Optional message'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign up'),
    ];
    $form['#theme'] = self::THEME_ID;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $sender = $form_state->getValue('email_address');
    $name = $form_state->getValue('name');
    $organization = $form_state->getValue('your_organization');
    $is_staff = $form_state->getValue('is_staff') === 1 ? 'Is staff of organization' : 'Is not staff of organization';
    $message = $form_state->getValue('message');
    $recipient = Settings::getBetaSignupRecipient();
    if (Drupal::service('email.validator')->isValid($sender)) {
      $email_message = [
        'headers' => [
          'content-type' => 'text/plain',
          'MIME-Version' => '1.0',
          'reply-to' => $sender,
          'from' => sprintf('%s <%s>', $name, $sender),
        ],
        'to' => $recipient,
        'subject' => sprintf('[Signup] from %s', $organization),
        'body' => sprintf("%s\n%s", $is_staff, $message),
      ];
      $mailer = new PhpMail();
      $mailer->mail($email_message);
      Drupal::logger('activeforanimals')->notice('Sent e-mail: <pre>' . htmlentities(print_r($email_message, TRUE)) . '</pre>');
      drupal_set_message($this->t('Thank you for signing up. Please allow for a few days to process your submission after which we will get in touch with you.'));
    }
    else {
      drupal_set_message($this->t('Unable to sign up, please try again later.'), 'error');
    }
  }
}
