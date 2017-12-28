<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for FilterOverview.
 */
class ExportOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $event_overview_link = NULL;
    $this->variables['content']['title'] = $element_controller->view(t('Filter'), 'title', $event_overview_link);
    $this->variables['content']['create_csv_link'] = $element_controller->view(t('Create a filter'), 'add_filter', new Url('activeforanimals.filter'));
    $this->variables['content']['empty'] = t('No filters created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['filters'] as $filter) {
      $filter_elements = [];
      $filter_link = new Url(
        'entity.filter.canonical', [
          'filter' => $filter->id(),
        ]
      );
      $create_date = Drupal::service('date.formatter')->format($filter->get('created')->value);
      $filter_elements['created'] = $element_controller->view($create_date, 'created');
      $filter_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $filter_link);
      $this->variables['content']['filters'][] = $filter_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
