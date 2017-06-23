<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Controller\Element\FieldController;
use Drupal\effective_activism\Controller\Element\ImageController;
use Drupal\effective_activism\Controller\Misc\ManagementToolboxController;
use Drupal\effective_activism\Controller\Misc\ContactInformationController;
use Drupal\effective_activism\Controller\Overview\EventListController;
use Drupal\effective_activism\Controller\Overview\GroupListController;

/**
 * Preprocessor for Organization.
 */
class OrganizationPreprocessor extends Preprocessor implements PreprocessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Organization Entity Object.
    $organization = $this->variables['elements']['#organization'];
    // Wrap elements.
    $image_controller = new ImageController();
    $field_controller = new FieldController();
    if (isset($organization->get('logo')->entity)) {
      $this->variables['content']['logo'] = $image_controller->view($organization->get('logo')->entity->getFileUri(), 'logo', ImageController::LOGO_200X200);
    }
    $this->variables['content']['title'] = $field_controller->view($organization->get('title'));
    $this->variables['content']['description'] = $field_controller->view($organization->get('description'));
    // Get contact information.
    $contact_information_controller = new ContactInformationController();
    $this->variables['content']['contact_information'] = $contact_information_controller->view($organization);
    $management_toolbox_controller = new ManagementToolboxController($organization);
    if ($management_toolbox_controller->access()) {
      $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    }
    // Get organization groups.
    $groups = OrganizationHelper::getGroups($organization, 0, GroupListController::GROUP_DISPLAY_LIMIT);
    $group_list_controller = new GroupListController([
      'organization' => $organization,
      'groups' => $groups,
    ]);
    $this->variables['content']['groups'] = $group_list_controller->view();
    // Get organization events.
    $events = OrganizationHelper::getEvents($organization, 0, EventListController::EVENT_DISPLAY_LIMIT);
    $event_list_controller = new EventListController([
      'events' => $events,
    ]);
    $this->variables['content']['events'] = $event_list_controller->view();
    // Add link to organization.
    $this->variables['content']['link'] = Drupal::l(
      $organization->label(),
      new Url(
        'entity.organization.canonical', [
          'organization' => $organization->id(),
        ],
        [
          'attributes' => [
            'class' => [
              'button',
            ],
          ],
        ]
      )
    );
    return $this->variables;
  }

}
