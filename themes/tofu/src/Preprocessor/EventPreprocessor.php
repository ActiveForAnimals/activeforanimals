<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

/**
 * Preprocessor for Event.
 */
class EventPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $event = $this->variables['elements']['#event'];
    // Wrap elements.
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $this->variables['content']['title'] = $field_controller->view($event->get('title'));
    $this->variables['content']['location'] = $field_controller->view($event->get('location'));
    $this->variables['content']['description'] = $field_controller->view($event->get('description'));
    $this->variables['content']['start_date'] = $field_controller->view($event->get('start_date'));
    $this->variables['content']['end_date'] = $field_controller->view($event->get('end_date'));
    $this->variables['content']['results'] = $field_controller->view($event->get('results'));
    $this->variables['content']['results'] = $field_controller->view($event->get('results'));
    // Get organization groups.
    $organization = $event->get('parent')->entity->get('organization')->entity;
    $groups = OrganizationHelper::getGroups($organization, 0, GroupListController::GROUP_DISPLAY_LIMIT);
    $group_list_controller = new GroupListController([
      'organization' => $organization,
      'groups' => $groups,
    ]);
    $this->variables['content']['groups'] = $group_list_controller->view();
    // Get group events.
    $group = $event->get('parent')->entity;
    $events = GroupHelper::getEvents($group, 0, EventListController::EVENT_DISPLAY_LIMIT);
    $event_list_controller = new EventListController([
      'group' => $group,
      'events' => $events,
    ]);
    $this->variables['content']['events'] = $event_list_controller->view();
    // Manager toolbox.
    $management_toolbox_controller = new ManagementToolboxController($event);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    // Organizer toolbox.
    $organizer_toolbox_controller = new OrganizerToolboxController($event);
    if ($organizer_toolbox_controller->access()) {
      $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    }
    return $this->variables;
  }

}
