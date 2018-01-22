<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;

/**
 * Preprocessor for event templates.
 */
class EventTemplatePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch EventTemplate Entity Object.
    $event_template = $this->variables['elements']['#event_template'];
    // Wrap elements.
    $element_controller = new ElementController();
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $this->variables['content']['title'] = $field_controller->view($event_template->get('name'));
    $this->variables['content']['event_title'] = $field_controller->view($event_template->get('event_title'));
    $this->variables['content']['event_description'] = $field_controller->view($event_template->get('event_description'));
    // Manager toolbox.
    $management_toolbox_controller = new ManagementToolboxController($event_template);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    return $this->variables;
  }

}
