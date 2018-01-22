<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\OrganizerToolboxController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

/**
 * Preprocessor for Group.
 */
class GroupPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $group = $this->variables['elements']['#group'];
    $this->variables['content']['logo'] = isset($group->get('logo')->entity) ? $this->imageRenderer->view($group->get('logo')->entity->getFileUri(), 'logo', ImageController::LOGO_200X200) : NULL;
    $this->variables['content']['title'] = $this->fieldRenderer->view($group->get('title'));
    $this->variables['content']['description'] = $this->fieldRenderer->view($group->get('description'));
    $this->variables['content']['website'] = $this->fieldRenderer->view($group->get('website'));
    $this->variables['content']['phone_number'] = $this->fieldRenderer->view($group->get('phone_number'));
    $this->variables['content']['email_address'] = $this->fieldRenderer->view($group->get('email_address'));
    $this->variables['content']['location'] = $this->fieldRenderer->view($group->get('location'));
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
