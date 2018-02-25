<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Event entities.
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
    $this->variables['content']['title'] = $event->get('title')->isEmpty() ? NULL : $this->wrapField($event->get('title'));
    $this->variables['content']['location'] = $event->get('location')->isEmpty() ? NULL : $this->wrapField($event->get('location'));
    $this->variables['content']['description'] = $event->get('description')->isEmpty() ? NULL : $this->wrapField($event->get('description'));
    $this->variables['content']['start_date'] = $event->get('start_date')->isEmpty() ? NULL : $this->wrapField($event->get('start_date'));
    $this->variables['content']['end_date'] = $event->get('end_date')->isEmpty() ? NULL : $this->wrapField($event->get('end_date'));
    $this->variables['content']['results'] = $event->get('results')->isEmpty() ? NULL : $this->wrapField($event->get('results'));
    $this->variables['content']['map'] = $this->wrapImage($this->getMap($event->get('location')->getValue()), 'map');
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render();
    // Add manager links.
    if (AccessControl::isManager($event->parent->entity->organization->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.event.edit_form', [
          'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($event->parent->entity->label()),
          'event' => $event->id(),
        ]
      ));
      $publish_state = $event->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.event.publish_form', [
          'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($event->parent->entity->label()),
          'event' => $event->id(),
        ]
      ));
    }
    // Add organizer links.
    elseif (AccessControl::isOrganizer($event->parent->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.event.edit_form', [
          'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($event->parent->entity->label()),
          'event' => $event->id(),
        ]
      ));
    }
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
