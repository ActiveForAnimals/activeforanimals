<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Helper\ImportHelper;

class ImportOverviewPreprocessor extends Preprocessor implements PreprocessorInterface {

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
    $this->variables['content']['title'] = $element_controller->view(t('Imports'), 'title', $event_overview_link);
    $this->variables['content']['create_csv_link'] = $element_controller->view(t('Import events from a CSV file'), 'add_import', new Url(
      'activeforanimals.import', [
        'import_type' => 'csv',
      ]
    ));
    $this->variables['content']['empty'] = t('No imports created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['imports'] as $import) {
      $import_elements =  [];
      $import_link = new Url(
        'entity.import.canonical', [
          'import' => $import->id(),
        ]);
      $import_elements['type'] = $element_controller->view($import->type->entity->label(), 'type');
      switch ($import->bundle()) {
        case 'csv':
          $import_elements['source'] = $element_controller->view($import->get('field_file_csv')->entity->getFilename(), 'source');
          break;

      }
      $create_date = Drupal::service('date.formatter')->format($import->get('created')->value);
      $import_elements['created'] = $element_controller->view($create_date, 'created');
      $import_elements['event_count'] = $element_controller->view(t('Events (@event_count)', [
          '@event_count' => count(ImportHelper::getEvents($import, 0, 0, FALSE)),
        ]), 'event_count');
      $import_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $import_link);
      $this->variables['content']['imports'][] = $import_elements;
    }
    $this->variables['content']['pager'] = [
      '#type' => 'pager',
    ];
    return $this->variables;
  }
}
