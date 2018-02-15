<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Organization entities.
 */
class OrganizationPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 5;

  /**
   * Event list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\EventListBuilder
   */
  protected $eventListBuilder;

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
    $this->eventListBuilder = new EventListBuilder(
      $this->entityTypeManager->getDefinition('event'),
      $this->entityTypeManager->getStorage('event'),
      $this->variables['elements']['#organization']
    );
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group'),
      $this->variables['elements']['#organization']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $organization = $this->variables['elements']['#organization'];
    $this->variables['content']['title'] = $this->wrapField($organization->get('title'));
    $this->variables['content']['description'] = $this->wrapField($organization->get('description'));
    $this->variables['content']['logo'] = !$organization->get('logo')->isEmpty() ?  $this->wrapImage(
      $organization->get('logo')->entity->getFileUri(),
      'logo',
      self::LOGO_200X200,
      new Url('entity.organization.canonical', ['organization' => PathHelper::transliterate($organization->label())])
    ) : NULL;
    $this->variables['content']['website'] = $this->wrapField($organization->get('website'));
    $this->variables['content']['phone_number'] = $this->wrapField($organization->get('phone_number'));
    $this->variables['content']['email_address'] = $this->wrapField($organization->get('email_address'));
    $this->variables['content']['location'] = $this->wrapField($organization->get('location'));
    //$this->variables['content']['groups'] = $this->groupListBuilder->render();
    //$this->variables['content']['events'] = $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render();
    //$management_toolbox_controller = new ManagementToolboxController($event);
    //if ($management_toolbox_controller->access()) {
    //  $this->variables['content']['management_toolbox'] = $management_toolbox_controller->view();
    //}
    // Organizer toolbox.
    //$organizer_toolbox_controller = new OrganizerToolboxController($event);
    //if ($organizer_toolbox_controller->access()) {
    //  $this->variables['content']['organizer_toolbox'] = $organizer_toolbox_controller->view();
    //}
    return $this->variables;
  }

}
