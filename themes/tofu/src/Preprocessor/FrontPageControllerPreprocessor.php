<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\activeforanimals\Form\NewsletterSignUpForm;
use Drupal\tofu\Constant;

/**
 * Preprocessor for FrontPageController.
 */
class FrontPageControllerPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $this->variables['content']['step_1'] = $this->wrapImage(sprintf('%s/images/front_page/step1.png', drupal_get_path('theme', Constant::MACHINE_NAME)), 'step');
    $this->variables['content']['step_2'] = $this->wrapImage(sprintf('%s/images/front_page/step2.png', drupal_get_path('theme', Constant::MACHINE_NAME)), 'step');
    $this->variables['content']['step_3'] = $this->wrapImage(sprintf('%s/images/front_page/step3.png', drupal_get_path('theme', Constant::MACHINE_NAME)), 'step');
    $this->variables['content']['newsletter_signup_form'] = Drupal::formBuilder()->getForm(NewsletterSignUpForm::class);
    return $this->variables;
  }

}
