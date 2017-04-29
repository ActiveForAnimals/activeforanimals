<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Element\FieldController;

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
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['logo'] = $field_controller->form($form['logo'], 'logo');
    $this->variables['form']['title'] = $field_controller->form($form['title'], 'title');
    $this->variables['form']['description'] = $field_controller->form($form['description'], 'description');
    $this->variables['form']['timezone'] = $field_controller->form($form['timezone'], 'timezone');
    $this->variables['form']['organizers'] = $field_controller->form($form['organizers'], 'inline_entity_form');
    // Get contact information.
    $contact_information_controller = new ContactInformationController();
    $this->variables['form']['contact_information'] = $contact_information_controller->form($form);
    return $this->variables;
  }

}
