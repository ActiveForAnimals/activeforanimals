<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

class GroupPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $group = $this->variables['elements']['#group'];
    // Wrap elements.
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    if (isset($group->get('logo')->entity)) {
      $this->variables['content']['logo'] = $image_controller->view($group->get('logo')->entity->getFileUri(), 'logo', ImageController::LOGO_200X200);
    }
    $this->variables['content']['title'] = $field_controller->view($group->get('title'));
    $this->variables['content']['description'] = $field_controller->view($group->get('description'));
    // Get contact information.
    $contact_information_controller = new ContactInformationController();
    $this->variables['content']['contact_information'] = $contact_information_controller->view($group);
    $management_toolbox_controller = new ManagementToolboxController($group);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    $organizer_toolbox_controller = new OrganizerToolboxController($group);
    if ($organizer_toolbox_controller->access()) {
      $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    }
    // Get organization groups.
    $groups = OrganizationHelper::getGroups($group->organization->entity, 0, GroupListController::GROUP_DISPLAY_LIMIT);
    $group_list_controller = new GroupListController([
      'organization' => $group->get('organization')->entity,
      'groups' => $groups,
    ]);
    $this->variables['content']['groups'] = $group_list_controller->view();
    // Get group events.
    $events = GroupHelper::getEvents($group, 0, EventListController::EVENT_DISPLAY_LIMIT);
    $event_list_controller = new EventListController([
      'group' => $group,
      'events' => $events,
    ]);
    $this->variables['content']['events'] = $event_list_controller->view();
    return $this->variables;
  }
}
