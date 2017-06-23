<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\Controller\Element\ElementController;

/**
 * Preprocessor for OrganizerToolbox.
 */
class OrganizerToolboxPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $entity = $this->variables['elements']['#storage']['entity'];
    // Fetch elements.
    $element_controller = new ElementController();
    switch ($entity->getEntityTypeId()) {
      case 'organization':
        $this->variables['content']['manage_groups'] = $element_controller->view(t('Manage groups'), 'manage_groups', new Url(
          'entity.organization.groups', [
            'organization' => $entity->id(),
          ]
        ));
        break;

      case 'group':
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
        break;

      case 'import':
        $this->variables['content']['edit_this_page'] = $element_controller->view(t('Edit this page'), 'edit_page', new Url(
          'entity.import.edit_form', [
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
        break;
    }
    $this->variables['content']['help_link'] = $element_controller->view(t('Help'), 'help_link', Url::fromUri('internal:#help',  [
      'attributes' => [
        'id' => 'activeforanimals_help',
      ],
    ]));
    return $this->variables;
  }

}
