<?php

namespace Drupal\afa_agarvenectarjar;

/**
 * Implements hook_form_alter().
 */
class Constant {

  /**
   * General constants.
   */
  const MODULE_NAME = 'afa_agarvenectarjar';

  /**
   * Form constants
   */
  const FORM_ID_LIST = [
    'user_register_form',
  ];
  const HIDDEN_FORM_ELEMENT_MACHINE_NAME = 'afa_username';
  const HIDDEN_FORM_ELEMENT_TITLE = 'Leave this field blank';
  const HIDDEN_VALIDATION_METHOD = '\Drupal\afa_agarvenectarjar\Helper\FormHelper::validateHiddenField';
  const HIDDEN_ELEMENT_NOTICE = 'Spambot access attempt - hidden field not empty';
  const SHOWN_FORM_ELEMENT_MACHINE_NAME = 'afa_challenge';
  const SHOWN_FORM_ELEMENT_TITLE = 'Please finish this sentence: "An orange is a fruit. An apple is also a _____."';
  const SHOWN_FORM_ELEMENT_DESCRIPTION = 'This question is asked to keep most robots from signing up. Just type "fruit" without quotes.';
  const SHOWN_VALIDATION_METHOD = '\Drupal\afa_agarvenectarjar\Helper\FormHelper::validateShownField';
  const SHOWN_ELEMENT_NOTICE = 'Spambot access attempt - challenge response is wrong';
  const SHOWN_ELEMENT_CORRECT_ANSWER = 'fruit';
  const FORM_VALIDATION_ERROR_MESSAGE = 'There was a problem with your form submission. Please refresh the page and try again.';

}
