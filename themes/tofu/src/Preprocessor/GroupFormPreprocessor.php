<?php

namespace Drupal\tofu\Preprocessor;

use Drupal;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\tofu\Constant;

/**
 * Preprocessor for Group add/edit page.
 */
class GroupFormPreprocessor extends Preprocessor implements PreprocessorInterface {

  const FORM_ID_EDIT_GROUP = 'group_edit_form';
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
    // Only display these things if the group exists.
    if ($variables['form']['#form_id'] === self::FORM_ID_EDIT_GROUP) {
      $this->eventListBuilder = new EventListBuilder(
        $this->entityTypeManager->getDefinition('event'),
        $this->entityTypeManager->getStorage('event')
      );
    }
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch form array.
    $form = $this->variables['form'];
    $this->variables['form']['logo'] = $this->wrapFormElement($form['logo'], 'logo');
    $this->variables['form']['title'] = $this->wrapFormElement($form['title'], 'title');
    $this->variables['form']['description'] = $this->wrapFormElement($form['description'], 'description');
    $this->variables['form']['timezone'] = $this->wrapFormElement($form['timezone'], 'timezone');
    $this->variables['form']['result_types'] = $this->wrapFormElement($form['result_types'], 'result_types');
    $this->variables['form']['organizers'] = $this->wrapFormElement($form['organizers'], 'inline_entity_form');
    $this->variables['form']['website'] = $this->wrapFormElement($form['website'], 'website');
    $this->variables['form']['phone_number'] = $this->wrapFormElement($form['phone_number'], 'phone_number');
    $this->variables['form']['email_address'] = $this->wrapFormElement($form['email_address'], 'email_address');
    $this->variables['form']['location'] = $this->wrapFormElement($form['location'], 'location');
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = isset($this->eventListBuilder) ? $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render() : NULL;
    // Entity will be NULL for group creation. In this case, we assume the user is a manager, as they have exclusive create access.
    $this->variables['is_manager'] = empty($entity) ? TRUE : AccountHelper::isManagerOfGroup($entity, Drupal::currentUser());
    return $this->variables;
  }

}
