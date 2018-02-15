<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
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
    if (!empty($this->variables['elements']['#storage']['entities']['group'])) {
      $event_overview_link = new Url(
      'entity.group.events', [
        'group' => $this->variables['elements']['#storage']['entities']['group']->id(),
      ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Events'), 'title', $event_overview_link);
    $this->variables['content']['create_link'] = $this->wrapElement(t('Create event'), 'add_event', new Url('activeforanimals.event.create'));
    $this->variables['content']['create_from_template_link'] = $this->wrapElement(t('Create event from template'), 'event_template', new Url('activeforanimals.event_template.select'));
    $this->variables['content']['empty'] = t('No events created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['events'] as $event) {
      $event_elements = [];
      $event_elements['title'] = !$event->get('title')->isEmpty() ? $this->wrapField($event->get('title')) : NULL;
      $event_elements['parent'] = !$event->get('parent')->isEmpty() ? $this->wrapField($event->get('parent')->entity->get('title'), new Url('entity.group.canonical', [
        Constant::SLUG_ORGANIZATION => PathHelper::transliterate($event->get('parent')->entity->organization->entity->label()),
        Constant::SLUG_GROUP => PathHelper::transliterate($event->get('parent')->entity->label()),
      ])) : NULL;
      $event_elements['start_date'] = !$event->get('parent')->isEmpty() ? $this->wrapField($event->get('start_date')) : NULL;
      $event_elements['location'] = !$event->get('location')->isEmpty() ? $this->wrapField($event->get('location')) : NULL;
      $event_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', new Url(
        'entity.event.canonical', [
          'event' => $event->id(),
      ]));
      $this->variables['content']['events'][] = $event_elements;
    }
    return $this->variables;
  }

}