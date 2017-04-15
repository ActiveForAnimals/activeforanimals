<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\ImportHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\ElementController;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

class ImportPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $import = $this->variables['elements']['#import'];
    // Wrap elements.
    $element_controller = new ElementController();
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    $this->variables['content']['parent'] = $field_controller->view($import->get('parent'));
    $this->variables['content']['type'] = $element_controller->view($import->type->entity->label(), 'type');
    switch ($import->bundle()) {
      case 'csv':
        $this->variables['content']['source'] = $element_controller->view($import->get('field_file_csv')->entity->getFilename(), 'source');
        break;

    }
    $create_date = Drupal::service('date.formatter')->format($import->get('created')->value);
    $this->variables['content']['created'] = $element_controller->view($create_date, 'created');
    $this->variables['content']['event_count'] = $element_controller->view(t('Events (@event_count)', [
      '@event_count' => count(ImportHelper::getEvents($import, 0, 0, FALSE)),
    ]), 'event_count');
    // Get organization groups.
    $organization = $import->get('parent')->entity->get('organization')->entity;
    $groups = OrganizationHelper::getGroups($organization, 0, GroupListController::GROUP_DISPLAY_LIMIT);
    $group_list_controller = new GroupListController([
      'organization' => $organization,
      'groups' => $groups,
    ]);
    $this->variables['content']['groups'] = $group_list_controller->view();
    // Manager toolbox.
    $management_toolbox_controller = new ManagementToolboxController($import);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    // Organizer toolbox.
    $organizer_toolbox_controller = new OrganizerToolboxController($import);
    if ($organizer_toolbox_controller->access()) {
      $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    }
    return $this->variables;
  }
}
