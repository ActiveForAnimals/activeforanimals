<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ElementController;

class ManagementToolboxPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $entity = $this->variables['elements']['#storage']['entity'];
    // Fetch elements.
    $element_controller = new ElementController();
    switch($entity->getEntityTypeId()) {
      case 'organization':
        $this->variables['content']['edit_this_page'] = $element_controller->view(t('Edit this page'), 'edit_page', new Url(
          'entity.organization.edit_form', [
            'organization' => $entity->id(),
          ]
        ));
        $this->variables['content']['manage_groups'] = $element_controller->view(t('Manage groups'), 'manage_groups', new Url(
          'entity.organization.groups', [
            'organization' => $entity->id(),
          ]
        ));
        $this->variables['content']['manage_results'] = $element_controller->view(t('Manage results'), 'manage_results', new Url('entity.result_type.collection'));
        $publish_state = $entity->isPublished() ? t('Unpublish') : t('Publish');
        $this->variables['content']['publish'] = $element_controller->view($publish_state, 'publish', new Url(
          'entity.organization.publish_form', [
            'organization' => $entity->id(),
          ]
        ));
        break;

      case 'group':
        $this->variables['content']['edit_this_page'] = $element_controller->view(t('Edit this page'), 'edit_page', new Url(
          'entity.group.edit_form', [
            'group' => $entity->id(),
          ]
        ));
        $this->variables['content']['manage_events'] = $element_controller->view(t('Manage events'), 'manage_events', new Url(
          'entity.group.events', [
            'group' => $entity->id(),
          ]
        ));
        $this->variables['content']['manage_imports'] = $element_controller->view(t('Manage imports'), 'manage_imports', new Url(
          'entity.group.imports', [
            'group' => $entity->id(),
          ]
        ));
        $this->variables['content']['manage_results'] = $element_controller->view(t('Manage results'), 'manage_results', new Url('entity.result_type.collection'));
        $publish_state = $entity->isPublished() ? t('Unpublish') : t('Publish');
        $this->variables['content']['publish'] = $element_controller->view($publish_state, 'publish', new Url(
          'entity.group.publish_form', [
            'group' => $entity->id(),
          ]
        ));
        break;

      case 'import':
        $this->variables['content']['edit_this_page'] = $element_controller->view(t('Edit this page'), 'edit_page', new Url(
          'entity.import.edit_form', [
            'import' => $entity->id(),
          ]
        ));
        $publish_state = $entity->isPublished() ? t('Unpublish') : t('Publish');
        $this->variables['content']['publish'] = $element_controller->view($publish_state, 'publish', new Url(
          'entity.import.publish_form', [
            'import' => $entity->id(),
          ]
        ));
        break;

      case 'event':
        $this->variables['content']['edit_this_page'] = $element_controller->view(t('Edit this page'), 'edit_page', new Url(
          'entity.event.edit_form', [
            'event' => $entity->id(),
          ]
        ));
        $publish_state = $entity->isPublished() ? t('Unpublish') : t('Publish');
        $this->variables['content']['publish'] = $element_controller->view($publish_state, 'publish', new Url(
          'entity.event.publish_form', [
            'event' => $entity->id(),
          ]
        ));
        break;
    }
    return $this->variables;
  }
}
