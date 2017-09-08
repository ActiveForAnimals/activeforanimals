<?php

namespace Drupal\afa_agarvenectarjar\Helper;

use Drupal;
use Drupal\afa_agarvenectarjar\Constant;
use Drupal\Core\Form\FormStateInterface;

/**
 * Helper class for AFA Algarvenectarjar-assisted forms.
 */
class FormHelper {

  /**
   * Validate hidden field.
   *
   * @param array $element
   *   An array of attributes for the element that is validated.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to validate.
   */
  public static function validateHiddenField(array $element, FormStateInterface $form_state) {
    // Make sure it's empty.
    if (!empty($element['#value'])) {
      Drupal::logger(Constant::MODULE_NAME)->notice(Constant::HIDDEN_ELEMENT_NOTICE);
      $form_state->setErrorByName('', Constant::FORM_VALIDATION_ERROR_MESSAGE);
    }
  }

  /**
   * Validate shown field.
   *
   * @param array $element
   *   An array of attributes for the element that is validated.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to validate.
   */
  public static function validateShownField(array $element, FormStateInterface $form_state) {
    // Make sure it's empty.
    if (empty($element['#value']) || $element['#value'] !== Constant::SHOWN_ELEMENT_CORRECT_ANSWER) {
      Drupal::logger(Constant::MODULE_NAME)->notice(sprintf('%s: %s', Constant::SHOWN_ELEMENT_NOTICE, $element['#value']));
      $form_state->setErrorByName('', Constant::FORM_VALIDATION_ERROR_MESSAGE);
    }
  }

}
