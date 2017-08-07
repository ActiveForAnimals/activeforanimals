<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for FrontPage.
 */
class ResultOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    return $this->variables;
  }

}
