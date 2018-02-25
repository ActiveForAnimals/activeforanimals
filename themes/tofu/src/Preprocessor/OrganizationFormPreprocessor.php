<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\Core\Url;
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
  const DATE_FORMAT = 'F jS, Y';

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
    // Only display these things if the organization exists.
    if ($variables['form']['#form_id'] === self::FORM_ID_EDIT_ORGANIZATION) {
      $this->eventListBuilder = new EventListBuilder(
        $this->entityTypeManager->getDefinition('event'),
        $this->entityTypeManager->getStorage('event')
      );
      $this->groupListBuilder = new GroupListBuilder(
        $this->entityTypeManager->getDefinition('group'),
        $this->entityTypeManager->getStorage('group')
      );
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
    foreach ($this->variables['form']['#invitations'] as $invitation) {
      $invitation_elements = [];
      $invitation_elements['email_address'] = $this->wrapElement($invitation->email, 'invitation_email');
      $invitation_elements['timestamp'] = $this->wrapElement(t('Invited on @date', [
        '@date' => date(self::DATE_FORMAT, $invitation->created),
      ]), 'invitation_timestamp');
      $invitation_elements['remove'] = $this->wrapElement(t('Remove'), 'invitation_remove', Url::fromRoute('effective_activism.invitation.remove', ['invitation' => $invitation->id]));
      $this->variables['content']['invitations']['items'][] = $invitation_elements;
    }
    $this->variables['content']['invitations']['empty_message'] = t('No current invitations');
    $this->variables['content']['invitations']['title'] = t('Current invitations');
    $this->variables['content']['groups'] = isset($this->groupListBuilder) ? $this->groupListBuilder->render() : NULL;
    $this->variables['content']['events'] = isset($this->eventListBuilder) ? $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render() : NULL;
    return $this->variables;
  }

}
