<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\EventTemplateHelper;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for EventTemplateListBuilder.
 */
class EventTemplateListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {

    $event_template_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $event_template_overview_link = new Url(
      'entity.organization.event_templates', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Event templates'), 'title', $event_template_overview_link);
    $this->variables['content']['create_link'] = $this->wrapElement(t('Create event template'), 'add_event_template', new Url(
      'entity.event_template.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]));
    $this->variables['content']['empty'] = t('No event templates created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['event_templates'] as $event_template) {
      $event_template_elements = [];
      $event_template_link = new Url(
        'entity.event_template.canonical', [
          'organization' => PathHelper::transliterate($event_template->organization->entity->label()),
          'event_template' => $event_template->id(),
        ]);
      $event_template_elements['event_count'] = $this->wrapElement(t('Events (@event_count)', [
        '@event_count' => count(EventTemplateHelper::getEvents($event_template, 0, 0, FALSE)),
      ]), 'event_count');
      $event_template_elements['title'] = $this->wrapField($event_template->name);
      $event_template_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', $event_template_link);
      $this->variables['content']['event_templates'][] = $event_template_elements;
    }
    return $this->variables;
  }

}
