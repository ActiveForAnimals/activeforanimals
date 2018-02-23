<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\FilterHelper;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for FilterListBuilder.
 */
class FilterListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $filter_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $filter_overview_link = new Url(
        'entity.organization.filters', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Filters'), 'title', $filter_overview_link);
    $this->variables['content']['create_link'] = $this->wrapElement(t('Create filter'), 'add_filter', new Url(
      'entity.filter.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]
    ));
    $this->variables['content']['empty'] = t('No filters created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['filters'] as $filter_id => $filter) {
      $filter_elements = [];
      $filter_link = new Url('entity.filter.canonical', [
        'organization' => PathHelper::transliterate($filter->organization->entity->label()),
        'filter' => $filter->id(),
      ]);
      $filter_elements['title'] = $this->wrapField($filter->get('name'), $filter_link);
      $filter_elements['event_count'] = $this->wrapElement(t('Events (@event_count)', [
        '@event_count' => count(FilterHelper::getEvents($filter, 0, 0, FALSE)),
      ]), 'event_count');
      $filter_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', $filter_link);
      $this->variables['content']['filters'][] = $filter_elements;
    }
    return $this->variables;
  }

}
