<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

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
    $create_date = Drupal::service('date.formatter')->format($filter->get('created')->value);
    $this->variables['content']['created'] = $element_controller->view($create_date, 'created');
    // Get organization groups.
    $organization = $filter->organization->entity;
    $groups = OrganizationHelper::getGroups($organization, 0, GroupListController::GROUP_DISPLAY_LIMIT);
    $group_list_controller = new GroupListController([
      'organization' => $organization,
      'groups' => $groups,
    ]);
    $this->variables['content']['groups'] = $group_list_controller->view();
    // Manager toolbox.
    $management_toolbox_controller = new ManagementToolboxController($filter);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    // Organizer toolbox.
    $organizer_toolbox_controller = new OrganizerToolboxController($filter);
    if ($organizer_toolbox_controller->access()) {
      $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    }
    return $this->variables;
  }

}
