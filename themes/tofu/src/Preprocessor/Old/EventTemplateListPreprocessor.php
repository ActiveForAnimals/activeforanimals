<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for EventTemplateList.
 */
class EventTemplateListPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $event_template_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $event_template_overview_link = new Url(
      'entity.organization.event_templates', [
        'organization' => $this->variables['elements']['#storage']['entities']['organization']->id(),
      ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Event templates'), 'title', $event_template_overview_link);
    $this->variables['content']['create_link'] = $element_controller->view(t('Create event template'), 'add_event_template', new Url('activeforanimals.event_template.create'));
    $this->variables['content']['empty'] = t('No event templates created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['event_templates'] as $event_template) {
      $event_template_elements = [];
      $event_template_link = new Url(
        'entity.event_template.canonical', [
          'event_template' => $event_template->id(),
        ]);
      $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => $event_template->organization->entity->id(),
        ]);
      $event_template_elements['name'] = $field_controller->view($event_template->name);
      if (!$event_template->get('organization')->isEmpty()) {
        $event_template_elements['organization'] = $field_controller->view($event_template->organization, $organization_link);
      }
      $event_template_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $event_template_link);
      $this->variables['content']['event_templates'][] = $event_template_elements;
    }
    return $this->variables;
  }

}
