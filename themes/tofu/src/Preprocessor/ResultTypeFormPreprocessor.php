<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Element\FieldController;

class ResultTypeFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    // Wrap form elements.
    $field_controller = new FieldController();
    $this->variables['form']['label'] = $field_controller->form($form['label'], 'title');
    $this->variables['form']['importname'] = $field_controller->form($form['importname'], 'importname');
    $this->variables['form']['description'] = $field_controller->form($form['description'], 'description');
    $this->variables['form']['datatypes'] = $field_controller->form($form['datatypes'], 'datatypes');
    $this->variables['form']['organization'] = $field_controller->form($form['organization'], 'organization');
    $this->variables['form']['groups'] = $field_controller->form($form['groups'], 'groups');
    return $this->variables;
  }
}
