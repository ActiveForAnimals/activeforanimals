<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Preprocessor abstract class.
 */
abstract class Preprocessor {

  /**
   * The variables to preprocess.
   */
  protected $variables;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    $this->variables = $variables;
  }

}
