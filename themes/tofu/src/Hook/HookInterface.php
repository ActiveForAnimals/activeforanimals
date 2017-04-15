<?php

namespace Drupal\tofu\Hook;

/**
 * Provides an interface for hook classes.
 *
 * @ingroup tofu
 */
interface HookInterface {

  /**
   * Return an instance of this class.
   *
   * @return HookImplementation
   *   Instance of this class.
   */
  public static function getInstance();

  /**
   * Invokes the hook.
   *
   * @param array $args
   *   The arguments of the hook.
   */
  public function invoke(array $args);

}
