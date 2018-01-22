<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Event.
 */
class EventPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 3;
  const GOOGLE_MAP_PARAMETER_TEMPLATE = '%s?markers=icon:%s|%f,%f&zoom=%d&size=640x200&scale=2&key=%s';
  const GOOGLE_MAP_BASE_URL = 'https://maps.googleapis.com/maps/api/staticmap';
  const GOOGLE_MAP_ZOOM_LEVEL = 15;

  /**
   * Group list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\GroupListBuilder
   */
  protected $groupListBuilder;

  /**
   * Event list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\EventListBuilder
   */
  protected $eventListBuilder;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group'),
      $this->variables['elements']['#event']->parent->entity->organization->entity
    );
    $this->eventListBuilder = new EventListBuilder(
      $this->entityTypeManager->getDefinition('event'),
      $this->entityTypeManager->getStorage('event'),
      $this->variables['elements']['#event']->parent->entity->organization->entity,
      $this->variables['elements']['#event']->parent->entity
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event = $this->variables['elements']['#event'];
    $this->variables['content']['title'] = $this->wrapField($event->get('title'));
    $this->variables['content']['location'] = $this->wrapField($event->get('location'));
    $this->variables['content']['description'] = $this->wrapField($event->get('description'));
    $this->variables['content']['start_date'] = $this->wrapField($event->get('start_date'));
    $this->variables['content']['end_date'] = $this->wrapField($event->get('end_date'));
    $this->variables['content']['results'] = $this->wrapField($event->get('results'));
    $this->variables['content']['results'] = $this->wrapField($event->get('results'));
    $this->variables['content']['map'] = $this->wrapImage($this->getMap($event->get('location')->getValue()), 'map');
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render();
    //$management_toolbox_controller = new ManagementToolboxController($event);
    //if ($management_toolbox_controller->access()) {
    //  $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    //}
    // Organizer toolbox.
    //$organizer_toolbox_controller = new OrganizerToolboxController($event);
    //if ($organizer_toolbox_controller->access()) {
    //  $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    //}
    return $this->variables;
  }

  /**
   * Returns a Google map image from a location.
   *
   * @param array $locations
   *   The locations to render as a map.
   *
   * @return string
   *   A map uri.
   */
  private function getMap(array $locations) {
    $location = array_pop($locations);
    return sprintf(
      self::GOOGLE_MAP_PARAMETER_TEMPLATE,
      self::GOOGLE_MAP_BASE_URL,
      sprintf(
        'https://%s/%s/images/location.png',
        Drupal::request()->getHost(),
        drupal_get_path('theme', Constant::MACHINE_NAME)
      ),
      $location['latitude'],
      $location['longitude'],
      self::GOOGLE_MAP_ZOOM_LEVEL,
      Drupal::config('effective_activism.settings')->get('google_static_maps_api_key')
    );
  }

}
