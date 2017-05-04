<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
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

  const GOOGLE_MAP_URL = 'https://maps.googleapis.com/maps/api/staticmap';
  const GOOGLE_MAP_ZOOM_LEVEL = 15;

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
    // Render map.
    $google_static_maps_api_key = Drupal::config('effective_activism.settings')->get('google_static_maps_api_key');
    $locations = $event->get('location')->getValue();
    $location = array_pop($locations);
    $map_uri = sprintf('%s?markers=%f,%f&zoom=%d&size=640x200&scale=2&key=%s', self::GOOGLE_MAP_URL, $location['latitude'], $location['longitude'], self::GOOGLE_MAP_ZOOM_LEVEL, $google_static_maps_api_key);
    $this->variables['content']['map'] = $image_controller->view($map_uri, 'map');
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
