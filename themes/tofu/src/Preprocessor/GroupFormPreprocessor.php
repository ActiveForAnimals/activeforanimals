<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Overview\InvitationOverviewController;
use Drupal\effective_activism\Helper\AccountHelper;

/**
 * Preprocessor for GroupForm.
 */
class GroupFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    $entity = Drupal::request()->attributes->get('group');
    // Entity will be NULL for group creation. In this case, we assume the user is a manager, as they have exclusive create access.
    $this->variables['is_manager'] = empty($entity) ? TRUE : AccountHelper::isManagerOfGroup($entity, Drupal::currentUser());
    // Wrap form elements.
    $field_controller = new FieldController();
    $element_controller = new ElementController();
    $this->variables['form']['logo'] = $field_controller->form($form['logo'], 'logo');
    $this->variables['form']['title'] = $field_controller->form($form['title'], 'title');
    $this->variables['form']['description'] = $field_controller->form($form['description'], 'description');
    $this->variables['form']['timezone'] = $field_controller->form($form['timezone'], 'timezone');
    $this->variables['form']['result_types'] = $field_controller->form($form['result_types'], 'result_types');
    $this->variables['form']['organizers'] = $field_controller->form($form['organizers'], 'inline_entity_form');
    // Get contact information.
    $contact_information_controller = new ContactInformationController();
    $this->variables['form']['contact_information'] = $contact_information_controller->form($form);
    // Get invitation list.
    $this->variables['form']['invitations'] = NULL;
    if (isset($this->variables['form']['#invitation_list'])) {
      $invitation_overview_controller = new InvitationOverviewController();
      $this->variables['form']['invitations'] = $invitation_overview_controller->content($this->variables['form']['#invitation_list']);
    }
    return $this->variables;
  }

}
