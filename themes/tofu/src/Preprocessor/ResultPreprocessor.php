<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for Organization add/edit page.
 */
class ResultPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $result = $this->variables['elements']['#result'];
    $this->variables['content']['title'] = $result->get('title')->isEmpty() ? NULL : $this->wrapField($result->get('title'));
    $this->variables['content']['participant_count'] = $result->get('participant_count')->isEmpty() ? NULL : $this->wrapField($result->get('participant_count'));
    $this->variables['content']['duration_minutes'] = $result->get('duration_minutes')->isEmpty() ? NULL : $this->wrapField($result->get('duration_minutes'));
    $this->variables['content']['duration_hours'] = $result->get('duration_hours')->isEmpty() ? NULL : $this->wrapField($result->get('duration_hours'));
    $this->variables['content']['duration_days'] = $result->get('duration_days')->isEmpty() ? NULL : $this->wrapField($result->get('duration_days'));
    return $this->variables;
  }

}
