<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ResultTypeForm.
 */
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
    $this->variables['help_button'] = [
      '#id' => 'activeforanimals_help',
      '#type' => 'button',
      '#value' => '',
      '#attributes' => [
        'title' => t('Help'),
      ],
    ];
    return $this->variables;
  }

}
