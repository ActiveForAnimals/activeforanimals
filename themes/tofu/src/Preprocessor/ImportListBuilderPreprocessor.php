<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for ImportListBuilder.
 */
class ImportListBuilderPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $import_overview_link = NULL;
    if (!empty($this->variables['elements']['#storage']['entities']['organization'])) {
      $import_overview_link = new Url(
        'entity.group.imports', [
          'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
          'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        ]);
    }
    $this->variables['content']['title'] = $this->wrapElement(t('Imports'), 'title', $import_overview_link);
    $this->variables['content']['create_csv_link'] = $this->wrapElement(t('Create CSV import'), 'add_import', new Url(
      'entity.import.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
        'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        'import_type' => 'csv',
      ]
    ));
    $this->variables['content']['create_icalendar_link'] = $this->wrapElement(t('Create iCalendar import'), 'add_import', new Url(
      'entity.import.add_form', [
        'organization' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['organization']->label()),
        'group' => PathHelper::transliterate($this->variables['elements']['#storage']['entities']['group']->label()),
        'import_type' => 'icalendar',
      ]
    ));
    $this->variables['content']['empty'] = t('No imports created yet.');
    $this->variables['content']['pager'] = $this->variables['elements']['pager'];
    foreach ($this->variables['elements']['#storage']['entities']['imports'] as $import) {
      $import_elements = [];
      $import_link = new Url(
        'entity.import.canonical', [
          'organization' => PathHelper::transliterate($import->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($import->parent->entity->label()),
          'import' => $import->id(),
        ]
      );
      $import_elements['created'] = $this->wrapElement(Drupal::service('date.formatter')->format($import->get('created')->value), 'created');
      $import_elements['more_info'] = $this->wrapButton(t('More info'), 'more_info', $import_link);
      $this->variables['content']['imports'][] = $import_elements;
    }
    return $this->variables;
  }

}
