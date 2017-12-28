<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ButtonController;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;

/**
 * Preprocessor for ExportList.
 */
class ExportListPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Wrap elements.
    $field_controller = new FieldController();
    $button_controller = new ButtonController();
    $element_controller = new ElementController();
    $export_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $export_overview_link = new Url(
      'entity.organization.exports', [
        'organization' => $this->variables['elements']['#storage']['entities']['organization']->id(),
      ]);
    }
    $this->variables['content']['title'] = $element_controller->view(t('Exports'), 'title', $export_overview_link);
    $this->variables['content']['create_link'] = $element_controller->view(t('Create export'), 'add_export', new Url(
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
        ]
      );
      $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => $export->organization->entity->id(),
        ]
      );
      if (!$export->organization->isEmpty()) {
        $export_elements['organization'] = $field_controller->view($export->organization, $organization_link);
      }
      if (!$export->filter->isEmpty()) {
        $export_elements['filter'] = $field_controller->view($export->filter);
      }
      $create_date = Drupal::service('date.formatter')->format($export->get('created')->value);
      $export_elements['created'] = $element_controller->view($create_date, 'created');
      $export_elements['more_info'] = $button_controller->view(t('More info'), 'more_info', $export_link);
      $this->variables['content']['exports'][] = $export_elements;
    }
    return $this->variables;
  }

}
