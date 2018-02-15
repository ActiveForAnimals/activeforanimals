<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Organization add/edit page.
 */
class OrganizationFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  const FORM_ID_EDIT_ORGANIZATION = 'organization_edit_form';
  const EVENT_LIST_LIMIT = 5;

  /**
   * Group list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\GroupListBuilder
   */
  protected $groupListBuilder;

  /**
   * Event list builder.
   *
   * @var \Drupal\effective_activism\ListBuilder\EventListBuilder
   */
  protected $eventListBuilder;

  /**
   * Constructor.
   */
  public function __construct(array $variables) {
    parent::__construct($variables);
    // Only display these things if the organization exists.
    if ($variables['form']['#form_id'] === self::FORM_ID_EDIT_ORGANIZATION) {
      /**$this->groupListBuilder = new GroupListBuilder(
        $this->entityTypeManager->getDefinition('group'),
        $this->entityTypeManager->getStorage('group'),
        $this->variables['form']['#entity']
      );
      $this->eventListBuilder = new EventListBuilder(
        $this->entityTypeManager->getDefinition('group'),
        $this->entityTypeManager->getStorage('group'),
        $this->variables['form']['#entity']
      );*/
      //$this->invitations = new InvitationOverview(
      //  $this->variables['form']['#invitation_list']
      //);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    $form = $this->variables['form'];
    $this->variables['form']['logo'] = $this->wrapFormElement($form['logo'], 'logo');
    $this->variables['form']['title'] = $this->wrapFormElement($form['title'], 'title');
    $this->variables['form']['description'] = $this->wrapFormElement($form['description'], 'description');
    $this->variables['form']['timezone'] = $this->wrapFormElement($form['timezone'], 'timezone');
    $this->variables['form']['managers'] = $this->wrapFormElement($form['managers'], 'inline_entity_form');
    $this->variables['form']['event_creation'] = $this->wrapFormElement($form['event_creation'], 'event_creation');
    $this->variables['form']['website'] = $this->wrapFormElement($form['website'], 'website');
    $this->variables['form']['phone_number'] = $this->wrapFormElement($form['phone_number'], 'phone_number');
    $this->variables['form']['email_address'] = $this->wrapFormElement($form['email_address'], 'email_address');
    $this->variables['form']['location'] = $this->wrapFormElement($form['location'], 'location');
    // Get invitation list.
    //$this->variables['form']['invitations'] = NULL;
    //$this->variables['form']['invitations'] = isset($this->invitations) ? $this->invitations->render() : NULL;
    $this->variables['content']['groups'] = isset($this->groupListBuilder) ? $this->groupListBuilder->render() : NULL;
    $this->variables['content']['events'] = isset($this->eventListBuilder) ? $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render() : NULL;
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
