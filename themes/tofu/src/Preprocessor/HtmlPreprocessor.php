<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\tofu\Constant;

/**
 * Preprocessor for HTML.
 */
class HtmlPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    return $this->variables;
  }

}
