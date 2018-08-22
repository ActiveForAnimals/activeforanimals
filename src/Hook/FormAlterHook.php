<?php

namespace Drupal\activeforanimals\Hook;

use Drupal;
use Drupal\Core\Form\FormStateInterface;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\PathHelper;

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
    elseif ($form_id === 'user_login_form') {
      $form['#submit'][] = [
        $this,
        'userLoginSubmitHandler',
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

  /**
   * Extra submit handler for the user login form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function userLoginSubmitHandler(array $form, FormStateInterface $form_state) {
    $user = Drupal::currentUser();
    $managed_organizations = AccountHelper::getManagedOrganizations($user);
    // If user is manager of multiple organizations, redirect to organization
    // overview.
    if (!empty($managed_organizations) && count($managed_organizations) > 1) {
      $form_state->setRedirect('entity.organization.collection');
      return;
    }
    // If user is manager of one organization, redirect to that one.
    if (!empty($managed_organizations) && count($managed_organizations) === 1) {
      $organization = array_pop($managed_organizations);
      $form_state->setRedirect('entity.organization.canonical', [
        'organization' => PathHelper::transliterate($organization->label()),
      ]);
      return;
    }
    $organized_groups = AccountHelper::getGroups($user);
    // If user is organizer of multiple groups, redirect to organization
    // group overview.
    // This does not take into account if user is organizer of groups of
    // different organizations.
    if (!empty($organized_groups) && count($organized_groups) > 1) {
      $group = array_pop($organized_groups);
      $form_state->setRedirect('entity.organization.groups', [
        'organization' => PathHelper::transliterate($group->organization->entity->label()),
      ]);
      return;
    }
    // If user is organizer of one group, redirect to that one.
    if (!empty($organized_groups) && count($organized_groups) === 1) {
      $group = array_pop($organized_groups);
      $form_state->setRedirect('entity.group.canonical', [
        'organization' => PathHelper::transliterate($group->organization->entity->label()),
        'group' => PathHelper::transliterate($group->label()),
      ]);
      return;
    }
    // If nothing else matches, redirect to front page.
    $form_state->setRedirect('<front>');
  }

}
