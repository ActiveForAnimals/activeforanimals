<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\tofu\Constant;

/**
 * Preprocessor for FrontPage.
 */
class FrontPagePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $image_controller = new ImageController();
    $step_1 = sprintf('%s/images/front_page/step1.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $step_2 = sprintf('%s/images/front_page/step2.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $step_3 = sprintf('%s/images/front_page/step3.png', drupal_get_path('theme', Constant::MACHINE_NAME));
    $this->variables['content']['step_1'] = $image_controller->view($step_1, 'step');
    $this->variables['content']['step_2'] = $image_controller->view($step_2, 'step');
    $this->variables['content']['step_3'] = $image_controller->view($step_3, 'step');
    return $this->variables;
  }

}
