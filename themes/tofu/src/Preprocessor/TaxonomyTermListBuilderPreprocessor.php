<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\effective_activism\Helper\TermHelper;

/**
 * Preprocessor for TaxonomyTermListBuilder.
 */
class TaxonomyTermListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $taxonomy_term_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $taxonomy_term_overview_link = new Url(
        'entity.organization.terms', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
        ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Tags'), 'title', $taxonomy_term_overview_link);
    $this->variables['content']['empty'] = t('No tags created yet.');
    $this->variables['content']['pager'] = $this->variables['elements']['pager'];
    foreach ($this->variables['elements']['#storage']['entities']['taxonomy_terms'] as $term) {
      $term_elements = [];
      $term_elements['title'] = $this->wrapElement($term->label(), 'title');
      $term_elements['event_count'] = $this->wrapElement(t('Events (@event_count)', [
        '@event_count' => count(TermHelper::getEvents($this->variables['elements']['#storage']['entities']['organization'], $term, 0, 0, FALSE)),
      ]), 'event_count');
      $this->variables['content']['terms'][] = $term_elements;
    }
    return $this->variables;
  }

}
