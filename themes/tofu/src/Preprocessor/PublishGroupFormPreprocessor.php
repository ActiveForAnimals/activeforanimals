<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor for PublishGroupForm.
 */
class PublishGroupFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    return $this->variables;
  }

}
