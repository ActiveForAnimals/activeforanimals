<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Entity\ResultType;

class DataFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $elements = $this->variables['elements'];
    // Wrap form elements.
    $element_controller = new ElementController();
    $field_controller = new FieldController();
    $result_type = ResultType::load($elements['#bundle']);
    $tag_field = sprintf('tags_%d', $result_type->organization);
    $this->variables['content']['title'] = $result_type->label();
    $this->variables['elements']['participant_count'] = $field_controller->form($elements['participant_count'], 'participant_count');
    $this->variables['elements']['duration_minutes'] = $field_controller->form($elements['duration_minutes'], 'duration_minutes');
    $this->variables['elements']['duration_hours'] = $field_controller->form($elements['duration_hours'], 'duration_hours');
    $this->variables['elements']['duration_days'] = $field_controller->form($elements['duration_days'], 'duration_days');
    $this->variables['elements']['tags'] = $field_controller->form($elements[$tag_field], 'tags');
    return $this->variables;
  }
}
