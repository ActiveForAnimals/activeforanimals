<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\ElementController;

/**
 * Preprocessor abstract class.
 */
abstract class Preprocessor {

  /**
   * The variables to preprocess.
   *
   * @var array
   */
  protected $variables;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    $this->variables = $variables;
    // Fetch elements.
    $element_controller = new ElementController();
  }

}
