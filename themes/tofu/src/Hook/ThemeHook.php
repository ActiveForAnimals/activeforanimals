<?php

namespace Drupal\tofu\Hook;

use Drupal;
use Drupal\tofu\Helper\ThemeHelper;

/**
 * Implements hook_theme().
 */
class ThemeHook implements HookInterface {

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
    $theme = [];
    $entity_type_manager = Drupal::entityTypeManager();
    $entity_types = $entity_type_manager->getDefinitions();
    // Define template for inline result forms.
    $theme['inline_entity_form_result'] = [
      'render element' => 'form',
      'template' => sprintf('%s/%s', 'Result', ThemeHelper::convertToClassName('inline_entity_form_result')),
    ];
    // Define templates for entities.
    foreach ($entity_types as $machine_name => $entity_type) {
      if ($entity_type->getProvider() === 'effective_activism') {
        $entity_class_name = ThemeHelper::convertToClassName($entity_type->id());
        $theme[$machine_name] = [
          'render element' => 'elements',
          'template' => sprintf('%s/%s', $entity_class_name, $entity_class_name),
        ];
        $handlers = $entity_type->get('handlers');
        // Add forms.
        if (!empty($handlers['form'])) {
          // Filter duplicates, as typical with add and edit form handlers. 
          $form_classes = array_unique(array_values($handlers['form']));
          foreach ($form_classes as $form_class) {
            $pieces = explode('\\', $form_class);
            $theme[end($pieces)] = [
              'render element' => 'form',
              'template' => sprintf('%s/%s', $entity_class_name, end($pieces)),
            ];
          }
        }
        // Add list builders.
        if (!empty($handlers['list_builder'])) {
          $pieces = explode('\\', $handlers['list_builder']);
          $theme[end($pieces)] = [
            'render element' => 'elements',
            'template' => sprintf('%s/%s', $entity_class_name, end($pieces)),
          ];
        }
      }
    }
    return $theme;
  }

}
