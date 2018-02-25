<?php

namespace Drupal\tofu\Hook;

use Drupal\tofu\Helper\ThemeHelper;

/**
 * Implements hook_preprocess().
 */
class PreprocessHook implements HookInterface {

  const PREPROCESSOR_CLASS_PATTERN = 'Drupal\tofu\Preprocessor\%sPreprocessor';

  /**
   * An instance of this class.
   *
   * @var HookImplementation
   */
  private static $instance;

  /**
   * {@inheritdoc}
   */
  public static function getInstance() {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * {@inheritdoc}
   */
  public function invoke(array $args) {
    $variables = $args['variables'];
    $hook = $args['hook'];
    $preprocessor_class = sprintf(self::PREPROCESSOR_CLASS_PATTERN, ThemeHelper::convertToClassName($hook));
    if (class_exists($preprocessor_class)) {
      $preprocessor = new $preprocessor_class($variables);
    }
    return !empty($preprocessor) ? $preprocessor->preprocess() : $variables;
  }

}
