<?php

namespace Drupal\tofu\Preprocessor;

use DateTime;
use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\DateHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Event entities.
 */
class EventPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 10;
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
    $now = DateHelper::getNow($event->parent->entity->organization->entity, $event->parent->entity);
    $start_date = new DateTime($event->start_date->value);
    $this->variables['content']['title'] = $event->get('title')->isEmpty() ? NULL : $this->wrapField($event->get('title'));
    $this->variables['content']['location'] = $event->get('location')->isEmpty() ? NULL : $this->wrapField($event->get('location'));
    $this->variables['content']['description'] = $event->get('description')->isEmpty() ? NULL : $this->wrapField($event->get('description'));
    $this->variables['content']['start_date'] = $event->get('start_date')->isEmpty() ? NULL : $this->wrapField($event->get('start_date'));
    $this->variables['content']['end_date'] = $event->get('end_date')->isEmpty() ? NULL : $this->wrapField($event->get('end_date'));
    $this->variables['content']['map'] = $this->wrapImage($this->getMap($event->get('location')->getValue()), 'map');
    if (
      AccessControl::isManager($event->parent->entity->organization->entity)->isAllowed() ||
      AccessControl::isOrganizer($event->parent->entity)->isAllowed()
    ) {
      if (!$event->results->isEmpty()) {
        $field_map = Drupal::service('entity_field.manager')->getFieldMap();
        foreach ($event->results as $item) {
          // Retrieve result data for result.
          $result_entity_display = entity_get_display('result', $item->entity->getType(), 'default');
          foreach ($field_map['result'] as $field_name => $field_data) {
            // Get fields that are visible and begin with 'data_'.
            // These contain entity references to the result data.
            if (
              substr($field_name, 0, 5) === 'data_' &&
              in_array($item->entity->getType(), $field_data['bundles']) &&
              $result_entity_display->getComponent($field_name) !== NULL
            ) {
              if (!$item->entity->get($field_name)->isEmpty()) {
                // Iterate all data entities connected to this result.
                foreach ($item->entity->get($field_name) as $data_item) {
                  // Get fields that begin with 'field_'.
                  // These contain the data values of the data entity.
                  foreach ($field_map['data'] as $data_field_name => $data_field_data) {
                    // Special case: amount and currency.
                    if (
                      $data_field_name === 'field_transaction' &&
                      in_array($data_item->entity->getType(), $data_field_data['bundles'])
                    ) {
                      $this->variables['content']['results'][$item->entity->getType()][strtolower($data_item->entity->get($data_field_name)->getFieldDefinition()->getLabel())] = $this->wrapElement(t('@type of @currency @transaction ', [
                        '@type' => $data_item->entity->get($data_field_name)->getFieldDefinition()->getLabel(),
                        '@currency' => strtoupper($data_item->entity->get('field_currency')->value),
                        '@transaction' => $data_item->entity->get($data_field_name)->value,
                      ]), 'data');
                    }
                    elseif ($data_field_name === 'field_currency') {
                      // Skip field.
                    }
                    elseif (
                      substr($data_field_name, 0, 6) === 'field_' &&
                      in_array($data_item->entity->getType(), $data_field_data['bundles'])
                    ) {
                      $this->variables['content']['results'][$item->entity->getType()][$data_field_name] = $this->wrapElement(t('@data @label', [
                        '@data' => $data_item->entity->get($data_field_name)->value,
                        '@label' => strtolower($data_item->entity->get($data_field_name)->getFieldDefinition()->getLabel()),
                      ]), 'data');
                    }
                  }
                }
              }
            }
          }
          $this->variables['content']['results'][$item->entity->getType()]['participants'] = $this->wrapElement(t('@participant_count participants', ['@participant_count' => $item->entity->participant_count->value]), 'participants');
          $this->variables['content']['results'][$item->entity->getType()]['duration'] = $this->wrapElement(t('Duration of @days days, @hours hours and @minutes minutes', [
            '@days' => $item->entity->duration_days->value,
            '@hours' => $item->entity->duration_hours->value,
            '@minutes' => $item->entity->duration_minutes->value,
          ]), 'duration');
          // Retrieve tags from result.
          $tags = [];
          foreach ($item->entity->get(sprintf('tags_%d', $event->parent->entity->organization->entity->id())) as $tag_item) {
            if (!$tag_item->isEmpty()) {
              $tags[] = $tag_item->entity->label();
            }
          }
          $this->variables['content']['results'][$item->entity->getType()]['tags'] = empty($tags) ? NULL : $this->wrapElement(t('Tags: @tags', ['@tags' => implode(', ', $tags)]), 'tags');
        }
      }
      if (!$event->third_party_content->isEmpty()) {
        foreach ($event->third_party_content as $item) {
          switch ($item->entity->getType()) {
            case 'weather_information':
              $this->variables['content']['weather_information']['temperature'] = $item->entity->get('field_temperature')->isEmpty() ? NULL : $this->wrapElement(t('Temperature: @temperature °C', ['@temperature' => round($item->entity->get('field_temperature')->value)]), 'temperature');
              $this->variables['content']['weather_information']['precipitation'] = ($item->entity->get('field_precipitation_type')->isEmpty() || $item->entity->get('field_precipitation_intensity')->isEmpty()) ? NULL : $this->wrapElement(t('Precipitation: @precipitation_intensity mm/h @precipitation_type', [
                '@precipitation_type' => $item->entity->get('field_precipitation_type')->value,
                '@precipitation_intensity' => $item->entity->get('field_precipitation_intensity')->value,
              ]), 'precipitation');
              $this->variables['content']['weather_information']['visibility'] = $item->entity->get('field_visibility')->isEmpty() ? NULL : $this->wrapElement(t('Visibility: @visibility km', ['@visibility' => $item->entity->get('field_visibility')->value]), 'visibility');
              $this->variables['content']['weather_information']['windspeed'] = $item->entity->get('field_windspeed')->isEmpty() ? NULL : $this->wrapElement(t('Windspeed: @windspeed m/s', ['@windspeed' => $item->entity->get('field_windspeed')->value]), 'windspeed');
              break;

            case 'demographics':
              $this->variables['content']['demographics']['total_population'] = $item->entity->get('field_total_population')->isEmpty() ? NULL : $this->wrapElement(t('Total population: @total_population', ['@total_population' => $item->entity->get('field_total_population')->value]), 'total_population');
              $this->variables['content']['demographics']['male_population'] = $item->entity->get('field_male_population')->isEmpty() ? NULL : $this->wrapElement(t('Male population: @male_population', ['@male_population' => $item->entity->get('field_male_population')->value]), 'male_population');
              $this->variables['content']['demographics']['female_population'] = $item->entity->get('field_female_population')->isEmpty() ? NULL : $this->wrapElement(t('Female population: @female_population', ['@female_population' => $item->entity->get('field_female_population')->value]), 'female_population');
              $this->variables['content']['demographics']['total_households'] = $item->entity->get('field_total_households')->isEmpty() ? NULL : $this->wrapElement(t('Total households: @total_households', ['@total_households' => $item->entity->get('field_total_households')->value]), 'total_households');
              $this->variables['content']['demographics']['average_household_size'] = $item->entity->get('field_average_household_size')->isEmpty() ? NULL : $this->wrapElement(t('Average household: @average_household_size', ['@average_household_size' => $item->entity->get('field_average_household_size')->value]), 'average_household_size');
              break;

            case 'extended_location_information':
              $this->variables['content']['extended_location_information']['gps_coordinates'] = $item->entity->get('field_latitude')->isEmpty() ? NULL : $this->wrapElement(t('GPS coordinates: @latitude, @longitude', [
                '@latitude' => substr($item->entity->get('field_latitude')->value, 0, 10),
                '@longitude' => substr($item->entity->get('field_longitude')->value, 0, 10),
              ]), 'gps_coordinates');
              $this->variables['content']['extended_location_information']['address_locality'] = $item->entity->get('field_address_locality')->isEmpty() ? NULL : $this->wrapElement(t('Locality: @address_locality', ['@address_locality' => $item->entity->get('field_address_locality')->value]), 'address_locality');
              $this->variables['content']['extended_location_information']['address_sublocality'] = $item->entity->get('field_address_sublocality')->isEmpty() ? NULL : $this->wrapElement(t('Sublocality: @address_sublocality', ['@address_sublocality' => $item->entity->get('field_address_sublocality')->value]), 'address_sublocality');
              $this->variables['content']['extended_location_information']['address_neighborhood'] = $item->entity->get('field_address_neighborhood')->isEmpty() ? NULL : $this->wrapElement(t('Neighborhood: @address_neighborhood', ['@address_neighborhood' => $item->entity->get('field_address_neighborhood')->value]), 'address_neighborhood');
              $this->variables['content']['extended_location_information']['address_postal_code'] = $item->entity->get('field_address_postal_code')->isEmpty() ? NULL : $this->wrapElement(t('Postal code: @address_postal_code', ['@address_postal_code' => $item->entity->get('field_address_postal_code')->value]), 'address_postal_code');
              break;
          }
        }
      }
    }
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder
      ->setLimit(self::EVENT_LIST_LIMIT)
      ->setSortAsc(TRUE)
      ->setTitle('Upcoming events')
      ->setFromDate($now)
      ->setEmpty('No upcoming events')
      ->setPagerIndex(1)
      ->render();
    // Add manager links.
    if (AccessControl::isManager($event->parent->entity->organization->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.event.edit_form', [
          'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($event->parent->entity->label()),
          'event' => $event->id(),
        ]
      ));
      if ($start_date->format('U') >= $now->format('U')) {
        $this->variables['content']['links']['repeat_event'] = $this->wrapElement(t('Repeat this event'), 'repeat_event', new Url(
          'entity.event.repeat_form', [
            'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
            'group' => PathHelper::transliterate($event->parent->entity->label()),
            'event' => $event->id(),
          ]
        ));
      }
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
      if ($start_date->format('U') >= $now->format('U')) {
        $this->variables['content']['links']['repeat_event'] = $this->wrapElement(t('Repeat this event'), 'repeat_event', new Url(
          'entity.event.repeat_form', [
            'organization' => PathHelper::transliterate($event->parent->entity->organization->entity->label()),
            'group' => PathHelper::transliterate($event->parent->entity->label()),
            'event' => $event->id(),
          ]
        ));
      }
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
