<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Overview\InvitationOverviewController;

/**
 * Preprocessor for OrganizationForm.
 */
class OrganizationFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['logo'] = $field_controller->form($form['logo'], 'logo');
    $this->variables['form']['title'] = $field_controller->form($form['title'], 'title');
    $this->variables['form']['description'] = $field_controller->form($form['description'], 'description');
    $this->variables['form']['timezone'] = $field_controller->form($form['timezone'], 'timezone');
    $this->variables['form']['managers'] = $field_controller->form($form['managers'], 'inline_entity_form');
    $this->variables['form']['event_creation'] = $field_controller->form($form['event_creation'], 'event_creation');
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
