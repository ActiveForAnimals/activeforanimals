<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for MyEventsController.
 */
class MyEventsControllerPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['content']['my_events'] = $this->variables['elements']['my_events'];
    return $this->variables;
  }

}
