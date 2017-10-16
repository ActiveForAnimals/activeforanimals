<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ExportOverview.
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
    if (!empty($this->variables['elements']['#storage']['entities']['group'])) {
      $event_overview_link = new Url(
      'entity.group.events', [
        'group' => $this->variables['elements']['#storage']['entities']['group']->id(),
      ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Export'), 'title', $event_overview_link);
    $this->variables['content']['create_csv_link'] = $element_controller->view(t('Export events to a CSV file'), 'add_export', new Url(
      'activeforanimals.export', [
        'export_type' => 'csv',
      ]
    ));
    $this->variables['content']['empty'] = t('No exports created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['exports'] as $export) {
      $export_elements = [];
      $export_link = new Url(
        'entity.export.canonical', [
          'export' => $export->id(),
        ]);
      $export_elements['type'] = $element_controller->view($export->type->entity->label(), 'type');
      switch ($export->bundle()) {
        case 'csv':
          if ($export->get('field_file_csv')->entity !== NULL) {
            $export_elements['source'] = $element_controller->view($export->get('field_file_csv')->entity->getFilename(), 'source');
          }
          break;

      }
      $create_date = Drupal::service('date.formatter')->format($export->get('created')->value);
      $export_elements['created'] = $element_controller->view($create_date, 'created');
      $export_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $export_link);
      $this->variables['content']['exports'][] = $export_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }

}
