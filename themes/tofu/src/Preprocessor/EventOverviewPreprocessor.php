<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Constant;

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
    $group = !empty($this->variables['elements']['#storage']['entities']['group']) ? $this->variables['elements']['#storage']['entities']['group'] : NULL;
    $organization = !empty($this->variables['elements']['#storage']['entities']['organization']) ? $this->variables['elements']['#storage']['entities']['organization'] : NULL;
    $event_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['group'])) {
      $event_overview_link = new Url(
      'entity.group.events', [
        'group' => $group->id(),
      ]);
    }
    // Determine which event creation links should be shown.
    $event_create_option = isset($organization) ? $organization->event_creation->value : $group->organization->entity->event_creation->value;
    $this->variables['content']['create_from_template_link'] = in_array($event_create_option, [
      Constant::EVENT_CREATION_ALL,
      Constant::EVENT_CREATION_EVENT_TEMPLATE,
    ]) ? $element_controller->view(t('Create an event template'), 'add_event_template', new Url('activeforanimals.event_template.create')) : NULL;
    $this->variables['content']['create_link'] = in_array($event_create_option, [
      Constant::EVENT_CREATION_ALL,
      Constant::EVENT_CREATION_EVENT,
    ]) ?  $element_controller->view(t('Create event'), 'add_event', new Url('activeforanimals.event.create')) : NULL;
    $this->variables['content']['title'] = $element_controller->view(t('Events'), 'title', $event_overview_link);
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
