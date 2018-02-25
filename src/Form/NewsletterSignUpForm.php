<?php

namespace Drupal\activeforanimals\Form;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\ClientException;
use ReflectionClass;

/**
 * Provides an invitation response form.
 */
class NewsletterSignUpForm extends FormBase {

  const FORM_ID = 'newsletter_signup';

  const API_BASE_URL = 'https://us16.api.mailchimp.com/3.0';
  const ADD_MEMBER_ENDPOINT = 'lists/%s/members/';

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
    $form['#theme'] = (new ReflectionClass($this))->getShortName();
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('E-mail address'),
      '#attributes' => [
        'placeholder' => $this->t('E-mail address'),
      ],
      '#required' => TRUE,
    ];
    $form['first_name'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('First name'),
      '#attributes' => [
        'placeholder' => $this->t('First name (optional)'),
      ],
    ];
    $form['last_name'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Last name'),
      '#attributes' => [
        'placeholder' => $this->t('Last name (optional)'),
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign up'),
      '#name' => 'sign-up',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $request = Drupal::httpClient()->request(
        'POST',
        sprintf('%s/%s', self::API_BASE_URL, sprintf(self::ADD_MEMBER_ENDPOINT, Drupal::config('effective_activism.settings')->get('mailchimp_list_id'))),
        [
          'auth' => [
            'afa',
            Drupal::config('effective_activism.settings')->get('mailchimp_api_key'),
          ],
          'json' => (object) [
            'email_address' => $form_state->getValue('email'),
            'status' => 'subscribed',
            'merge_fields' => (object) [
              'FNAME' => $form_state->getValue('first_name'),
              'LNAME' => $form_state->getValue('last_name'),
            ],
          ],
        ]
      );
      if ($request->getStatusCode() === 200) {
        drupal_set_message($this->t('You have been signed up to our newsletter.'));
        Drupal::logger('activeforanimals')->info(sprintf('Signed up user to newsletter'));
      }
      else {
        drupal_set_message($this->t('Failed to sign up to the newsletter. Please try again later.'), 'error');
        Drupal::logger('activeforanimals')->warning(sprintf('Failed to sign up user: Statuscode %s', $request->getStatusCode()));
      }
    }
    catch (BadResponseException $exception) {
      drupal_set_message($this->t('Failed to sign up to the newsletter. Please try again later.'), 'error');
      Drupal::logger('activeforanimals')->warning(sprintf('Failed to sign up user: Bad response - %s', $exception->getMessage()));
    }
    catch (RequestException $exception) {
      drupal_set_message($this->t('Failed to sign up to the newsletter. Please try again later.'), 'error');
      Drupal::logger('activeforanimals')->warning(sprintf('Failed to sign up user: Request - %s', $exception->getMessage()));
    }
    catch (ClientException $exception) {
      drupal_set_message($this->t('Failed to sign up to the newsletter. Please try again later.'), 'error');
      Drupal::logger('activeforanimals')->warning(sprintf('Failed to sign up user: Client - %s', (string) $exception->getResponse()->getBody()));
    }
  }

}
