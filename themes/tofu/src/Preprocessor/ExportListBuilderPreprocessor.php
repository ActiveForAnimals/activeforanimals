<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for ExportListBuilder.
 */
class ExportListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $export_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $export_overview_link = new Url(
        'entity.organization.exports', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
      ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Exports'), 'title', $export_overview_link);
    $this->variables['content']['create_link'] = $this->wrapElement(t('Create export'), 'add_export', new Url(
      'entity.export.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
        'export_type' => 'csv',
      ]
    ));
    $this->variables['content']['empty'] = t('No exports created yet.');
    foreach ($this->variables['elements']['#storage']['entities']['exports'] as $export) {
      $export_elements = [];
      $export_link = new Url(
        'entity.export.canonical', [
          'organization' => PathHelper::transliterate($export->organization->entity->label()),
          'export' => $export->id(),
        ]
      );
      $organization_link = new Url(
        'entity.organization.canonical', [
          'organization' => PathHelper::transliterate($export->organization->entity->label()),
        ]
      );
      $export_elements['filter'] = $export->filter->isEmpty() ? NULL : $this->wrapField($export->filter);
      $export_elements['created'] = $this->wrapElement(Drupal::service('date.formatter')->format($export->get('created')->value), 'created');
      $export_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', $export_link);
      $this->variables['content']['exports'][] = $export_elements;
    }
    return $this->variables;
  }

}
