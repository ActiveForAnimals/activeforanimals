<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for event templates.
 */
class EventTemplatePreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $event_template = $this->variables['elements']['#event_template'];
    $this->variables['content']['title'] = $this->wrapField($event_template->get('name'));
    $this->variables['content']['event_title'] = $this->wrapField($event_template->get('event_title'));
    $this->variables['content']['event_description'] = $this->wrapField($event_template->get('event_description'));
    // Add manager links.
    if (AccessControl::isManager($event_template->get('organization')->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.event_template.edit_form', [
          'organization' => PathHelper::transliterate($event_template->organization->entity->label()),
          'event_template' => $event_template->id(),
        ]
      ));
      $publish_state = $event_template->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.event_template.publish_form', [
          'organization' => PathHelper::transliterate($event_template->organization->entity->label()),
          'event_template' => $event_template->id(),
        ]
      ));
      $this->variables['content']['links']['delete'] = $this->wrapElement(t('Delete'), 'delete', new Url(
        'entity.event_template.delete_form', [
          'organization' => PathHelper::transliterate($event_template->organization->entity->label()),
          'event_template' => $event_template->id(),
        ]
      ));
    }
    return $this->variables;
  }

}
