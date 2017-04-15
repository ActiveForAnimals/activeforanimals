<?php

namespace Drupal\tofu\Preprocessor;

abstract class Preprocessor {

  /**
   * @var array
   *   The variables to preprocess.
   */
  protected $variables;

  public function __construct(array $variables) {
    $this->variables = $variables;
  }

}
