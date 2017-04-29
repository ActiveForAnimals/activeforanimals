<?php

namespace Drupal\tofu\Preprocessor;

/**
 * Interface for preprocessor classes.
 */
interface PreprocessorInterface {

  /**
   * Prepares variables for Organization templates.
   *
   * @return array
   *   An associative array containing:
   *   - elements: An associative array containing the user information and any
   *   - attributes: HTML attributes for the containing element.
   */
  public function preprocess();

}
