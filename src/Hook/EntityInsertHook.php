<?php

namespace Drupal\activeforanimals\Hook;

use Drupal\activeforanimals\Helper\PathAliasHelper;

/**
 * Implements hook_entity_insert().
 */
class EntityInsertHook implements HookInterface {

  /**
   * An instance of this class.
   *
   * @var HookImplementation $instance
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
    $entity = $args['entity'];
    PathAliasHelper::add($entity);
  }

}
