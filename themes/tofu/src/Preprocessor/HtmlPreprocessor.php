<?php

namespace Drupal\tofu\Preprocessor;

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
