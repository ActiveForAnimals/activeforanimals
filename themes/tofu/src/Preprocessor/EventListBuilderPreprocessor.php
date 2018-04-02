<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Constant;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for EventListBuilder.
 */
class EventListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event_overview_link = NULL;
    $event_add_link = NULL;
    $event_add_from_template_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['group'])) {
      $event_overview_link = new Url(
        'entity.group.events', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
          'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        ]);
      $event_create_option = $this->variables['elements']['#storage']['entities']['organization']->event_creation->value;
      // Only add create event link if enabled.
      if (in_array($event_create_option, [
        Constant::EVENT_CREATION_ALL,
        Constant::EVENT_CREATION_EVENT,
      ])) {
        $event_add_link = new Url('entity.event.add_form', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
          'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        ]);
      }
      // Only add create from event template link if enabled.
      if (in_array($event_create_option, [
        Constant::EVENT_CREATION_ALL,
        Constant::EVENT_CREATION_EVENT_TEMPLATE,
      ])) {
        $event_add_from_template_link = new Url('entity.event.add_from_template', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
          'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        ]);
      }
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Events'), 'title', $event_overview_link);
    $this->variables['content']['create_link'] = (!empty($event_add_link) && AccessControl::isGroupStaff([$this->variables['elements']['#storage']['entities']['group']])->isAllowed()) ? $this->wrapElement(t('Create event'), 'add_event', $event_add_link) : NULL;
    $this->variables['content']['create_from_template_link'] = (!empty($event_add_from_template_link) && AccessControl::isGroupStaff([$this->variables['elements']['#storage']['entities']['group']])->isAllowed()) ? $this->wrapElement(t('Create event from template'), 'event_template', $event_add_from_template_link) : NULL;
    $this->variables['content']['empty'] = t('No events created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['events'] as $event) {
      $event_elements = [];
      $event_elements['title'] = !$event->get('title')->isEmpty() ? $this->wrapField($event->get('title')) : NULL;
      $event_elements['parent'] = !$event->get('parent')->isEmpty() ? $this->wrapField($event->get('parent')->entity->get('title'), new Url('entity.group.canonical', [
        'organization' => PathHelper::transliterate($event->get('parent')->entity->organization->entity->label()),
        'group' => PathHelper::transliterate($event->get('parent')->entity->label()),
      ])) : NULL;
      $event_elements['start_date'] = !$event->get('parent')->isEmpty() ? $this->wrapField($event->get('start_date')) : NULL;
      $event_elements['location'] = !$event->get('location')->isEmpty() ? $this->wrapField($event->get('location')) : NULL;
      $event_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', new Url(
        'entity.event.canonical', [
          'organization' => PathHelper::transliterate($event->get('parent')->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($event->get('parent')->entity->label()),
          'event' => $event->id(),
        ]));
      $this->variables['content']['events'][] = $event_elements;
    }
    return $this->variables;
  }

}
