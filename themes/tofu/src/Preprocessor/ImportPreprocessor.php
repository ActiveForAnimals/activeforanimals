<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\ImportHelper;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for Import.
 */
class ImportPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * Group list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\GroupListBuilder
   */
  protected $groupListBuilder;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $import = $this->variables['elements']['#import'];
    $this->variables['content']['type'] = $this->wrapElement($import->type->entity->label(), 'type');
    $this->variables['content']['created'] = $this->wrapElement(Drupal::service('date.formatter')->format($import->get('created')->value), 'created');
    $this->variables['content']['event_count'] = $this->wrapElement(t('Events (@event_count)', [
      '@event_count' => count(ImportHelper::getEvents($import, 0, 0, FALSE)),
    ]), 'event_count');
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    switch ($import->bundle()) {
      case 'csv':
        $this->variables['content']['source'] = $this->wrapElement($import->get('field_file_csv')->entity->getFilename(), 'source');
        break;

    }
    // Add manager links.
    if (AccessControl::isManager($import->parent->entity->organization->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.import.edit_form', [
          'organization' => PathHelper::transliterate($import->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($import->parent->entity->label()),
          'import' => $import->id(),
        ]
      ));
      $publish_state = $import->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.import.publish_form', [
          'organization' => PathHelper::transliterate($import->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($import->parent->entity->label()),
          'import' => $import->id(),
        ]
      ));
    }
    // Add organizer links.
    elseif (AccessControl::isOrganizer($import->parent->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.import.edit_form', [
          'organization' => PathHelper::transliterate($import->parent->entity->organization->entity->label()),
          'group' => PathHelper::transliterate($import->parent->entity->label()),
          'import' => $import->id(),
        ]
      ));
    }
    return $this->variables;
  }

}
