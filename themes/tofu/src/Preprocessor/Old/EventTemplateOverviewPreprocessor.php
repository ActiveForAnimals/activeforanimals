<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventTemplateOverview.
 */
class EventTemplateOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $this->variables['content']['title'] = $element_controller->view(t('Event templates'), 'title');
    $this->variables['content']['create_event_template_link'] = $element_controller->view(t('Create an event template'), 'add_event_template', new Url('activeforanimals.event_template.create'));
    $this->variables['content']['empty'] = t('No event templates created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['event_templates'] as $event_template) {
      $event_template_elements = [];
      $event_template_link = new Url(
        'entity.event_template.canonical', [
          'event_template' => $event_template->id(),
        ]
      );
      $event_template_elements['title'] = $field_controller->view($event_template->name);
      $event_template_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $event_template_link);
      $this->variables['content']['event_templates'][] = $event_template_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
