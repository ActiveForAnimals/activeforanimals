<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for FilterOverview.
 */
class FilterOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $this->variables['content']['title'] = $element_controller->view(t('Filters'), 'title');
    $this->variables['content']['create_filter_link'] = $element_controller->view(t('Create a filter'), 'add_filter', new Url('activeforanimals.filter.create'));
    $this->variables['content']['empty'] = t('No filters created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['filters'] as $filter) {
      $filter_elements = [];
      $filter_link = new Url(
        'entity.filter.canonical', [
          'filter' => $filter->id(),
        ]
      );
      $filter_elements['title'] = $field_controller->view($filter->name);
      $filter_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $filter_link);
      $this->variables['content']['filters'][] = $filter_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
