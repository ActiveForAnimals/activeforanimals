<?php

/**
 * @file
 * Contains module hook implements.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\afa_agarvenectarjar\Hook\FormAlterHook;

/**
 * Implements hook_form_alter().
 */
function afa_agarvenectarjar_form_alter(&$form, FormStateInterface &$form_state, $form_id) {
  $hook = FormAlterHook::getInstance();
  return $hook->invoke([
    'form' => &$form,
    'form_state' => &$form_state,
    'form_id' => $form_id,
  ]);
}
