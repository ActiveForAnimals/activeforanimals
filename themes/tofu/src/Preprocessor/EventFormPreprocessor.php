<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\tofu\Constant;

/**
 * Preprocessor for EventForm.
 */
class EventFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 3;
  const GOOGLE_MAP_PARAMETER_TEMPLATE = '%s?markers=icon:%s|%f,%f&zoom=%d&size=640x200&scale=2&key=%s';
  const GOOGLE_MAP_BASE_URL = 'https://maps.googleapis.com/maps/api/staticmap';
  const GOOGLE_MAP_ZOOM_LEVEL = 15;

  /**
   * Event list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\EventListBuilder
   */
  protected $eventListBuilder;

  /**
   * Group list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\GroupListBuilder
   */
  protected $groupListBuilder;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    $this->eventListBuilder = new EventListBuilder(
      $this->entityTypeManager->getDefinition('event'),
      $this->entityTypeManager->getStorage('event')
    );
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event = Drupal::request()->get('event');
    $this->variables['form']['title'] = $this->wrapFormElement($this->variables['form']['title'], 'title');
    $this->variables['form']['description'] = $this->wrapFormElement($this->variables['form']['description'], 'description');
    $this->variables['form']['location'] = $this->wrapFormElement($this->variables['form']['location'], 'location');
    $this->variables['form']['start_date'] = $this->wrapFormElement($this->variables['form']['start_date'], 'start_date');
    $this->variables['form']['end_date'] = $this->wrapFormElement($this->variables['form']['end_date'], 'end_date');
    $this->variables['form']['results'] = $this->wrapFormElement($this->variables['form']['results'], 'inline_entity_form');
    $this->variables['content']['map'] = empty($event) ? NULL : $this->wrapImage($this->getMap($event->get('location')->getValue()), 'map');
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render();
    $this->variables['help_button'] = [
      '#id' => 'activeforanimals_help',
      '#type' => 'button',
      '#value' => '',
      '#attributes' => [
        'title' => t('Help'),
      ],
    ];
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
