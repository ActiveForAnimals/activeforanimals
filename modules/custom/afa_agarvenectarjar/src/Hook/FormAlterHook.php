<?php

namespace Drupal\afa_agarvenectarjar\Hook;

use Drupal\afa_agarvenectarjar\Constant;

/**
 * Implements hook_form_alter().
 */
class FormAlterHook implements HookInterface {

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
    $form = &$args['form'];
    $form_id = $args['form_id'];
    // Adds a hidden form element to attempt tricking spambots.
    if (in_array($form_id, Constant::HIDDEN_ELEMENT_LIST)) {
      $form[Constant::HIDDEN_FORM_ELEMENT_MACHINE_NAME] = [
        '#type' => 'textfield',
        '#title' => Constant::HIDDEN_FORM_ELEMENT_TITLE,
        '#size' => 20,
        '#weight' => -100,
        '#attributes' => ['autocomplete' => 'off'],
        '#element_validate' => [Constant::HIDDEN_VALIDATION_METHOD],
        '#prefix' => sprintf('<div class="%s">', str_replace('_', '-', Constant::HIDDEN_FORM_ELEMENT_MACHINE_NAME)),
        '#suffix' => '</div>',
        '#attached' => [
          'html_head' => [
            [
              [
                '#tag' => 'style',
                '#value' => sprintf('.%s { display: none !important; }', str_replace('_', '-', Constant::HIDDEN_FORM_ELEMENT_MACHINE_NAME)),
              ],
              Constant::MODULE_NAME,
            ],
          ],
        ],
      ];
    }
    // Adds a challenge element to attempt filtering spambots.
    if (in_array($form_id, Constant::CHALLENGE_ELEMENT_LIST)) {
      $form[Constant::SHOWN_FORM_ELEMENT_MACHINE_NAME] = [
        '#type' => 'textfield',
        '#title' => Constant::SHOWN_FORM_ELEMENT_TITLE,
        '#description' => Constant::SHOWN_FORM_ELEMENT_DESCRIPTION,
        '#size' => 20,
        '#weight' => -100,
        '#attributes' => ['autocomplete' => 'off'],
        '#element_validate' => [Constant::SHOWN_VALIDATION_METHOD],
      ];
    }
  }

}
