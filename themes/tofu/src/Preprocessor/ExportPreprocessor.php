<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for Export.
 */
class ExportPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $export = $this->variables['elements']['#export'];
    $this->variables['content']['filter'] = $export->get('filter')->isEmpty() ? NULL : $this->wrapElement($export->get('filter')->entity->label(), 'filter');
    switch ($export->bundle()) {
      case 'csv':
        $this->variables['content']['field_file_csv'] = $export->get('field_file_csv')->isEmpty() ? NULL : $this->wrapElement(t('@filename (Download)', [
          '@filename' => $export->get('field_file_csv')->entity->getFilename(),
        ]), 'file', Url::fromUri(file_create_url($export->get('field_file_csv')->entity->getFileUri())));
        break;

    }
    // Add manager links.
    if (AccessControl::isManager($export->get('organization')->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.export.edit_form', [
          'organization' => PathHelper::transliterate($export->get('organization')->entity->label()),
          'export' => $export->id(),
        ]
      ));
      $publish_state = $export->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.export.publish_form', [
          'organization' => PathHelper::transliterate($export->get('organization')->entity->label()),
          'export' => $export->id(),
        ]
      ));
    }
    return $this->variables;
  }

}
