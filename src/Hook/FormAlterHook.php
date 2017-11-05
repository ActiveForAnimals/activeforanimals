<?php

namespace Drupal\activeforanimals\Hook;

use Drupal;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
class FormAlterHook implements HookInterface {

  const MAX_LENGTH = 4096;

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
    $form = $args['form'];
    $form_id = $args['form_id'];
    if ($form_id === 'contact_message_feedback_general_form') {
      $form['actions']['submit']['#submit'][] = [
        $this,
        'feedbackSubmitHandler',
      ];
    }
    return $form;
  }

  /**
   * Extra submit handler for general feedback contact forms.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function feedbackSubmitHandler(array $form, FormStateInterface $form_state) {
    $rating_array = $form_state->getValue('field_feedback_rating');
    $rating = $rating_array[0]['value'];
    $message_array = $form_state->getValue('field_feedback_message');
    $message = substr(check_markup($message_array[0]['value']), 0, self::MAX_LENGTH);
    $user_id = Drupal::currentUser()->id();
    Drupal::logger('activeforanimals')->notice(sprintf('User %s submitted feedback. Rating: %d. Message: %s', $user_id, $rating, $message));
  }

}
