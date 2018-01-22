<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventOverview.
 */
class EventOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $event_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['group'])) {
      $event_overview_link = new Url(
      'entity.group.events', [
        'group' => $this->variables['elements']['#storage']['entities']['group']->id(),
      ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Events'), 'title', $event_overview_link);
    $this->variables['content']['create_link'] = $element_controller->view(t('Create event'), 'add_event', new Url('activeforanimals.event.create'));
    $this->variables['content']['create_from_template_link'] = $element_controller->view(t('Create event from template'), 'event_template', new Url('activeforanimals.event_template.select'));
    $this->variables['content']['empty'] = t('No events created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['events'] as $event) {
      $event_elements = [];
      $event_link = new Url(
        'entity.event.canonical', [
          'event' => $event->id(),
        ]);
      $group_link = new Url(
        'entity.group.canonical', [
          'group' => $event->get('parent')->entity->id(),
        ]);
      if (!$event->get('title')->isEmpty()) {
        $event_elements['title'] = $field_controller->view($event->get('title'));
      }
      if (!$event->get('parent')->isEmpty()) {
        $event_elements['parent'] = $field_controller->view($event->get('parent'), $group_link);
      }
      if (!$event->get('parent')->isEmpty()) {
        $event_elements['start_date'] = $field_controller->view($event->get('start_date'));
      }
      if (!$event->get('location')->isEmpty()) {
        $event_elements['location'] = $field_controller->view($event->get('location'));
      }
      $event_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $event_link);
      $this->variables['content']['events'][] = $event_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
