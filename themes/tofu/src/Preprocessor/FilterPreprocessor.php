<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;

/**
 * Preprocessor for Filter.
 */
class FilterPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Filter Entity Object.
    $filter = $this->variables['elements']['#filter'];
    // Wrap elements.
    $element_controller = new ElementController();
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $this->variables['content']['organization'] = $field_controller->view($filter->get('organization'));
    $this->variables['content']['title'] = $field_controller->view($filter->get('name'));
    // Manager toolbox.
    $management_toolbox_controller = new ManagementToolboxController($filter);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    return $this->variables;
  }

}
