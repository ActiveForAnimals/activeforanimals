<?php

namespace Drupal\tofu\Helper;

/**
 * Helper functions for theme and preprocessing classes.
 */
class ThemeHelper {

  /**
   * Convert a hook-formatted string to a PHP class name.
   *
   * @param string $hook
   *   A hook to convert.
   *
   * @return string
   *   A class name representation of the hook.
   */
  public static function convertToClassName($hook) {
    $pieces = explode('_', $hook);
    $capitalized_pieces = array_map(function ($piece) {
      return ucfirst($piece);
    }, $pieces);
    return implode('', $capitalized_pieces);
  }

}
