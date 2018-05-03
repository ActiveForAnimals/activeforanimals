<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\PathHelper;

/**
 * Preprocessor for Group.
 */
class GroupPreprocessor extends Preprocessor implements PreprocessorInterface {

  const EVENT_LIST_LIMIT = 3;

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
      $this->entityTypeManager->getStorage('event')
    );
    $this->groupListBuilder = new GroupListBuilder(
      $this->entityTypeManager->getDefinition('group'),
      $this->entityTypeManager->getStorage('group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess() {
    // Fetch Group Entity Object.
    $group = $this->variables['elements']['#group'];
    $this->variables['content']['logo'] = $group->get('logo')->isEmpty() ? NULL : $this->wrapImage($group->get('logo')->entity->getFileUri(), 'logo', self::LOGO_200X200);
    $this->variables['content']['title'] = $group->get('title')->isEmpty() ? NULL : $this->wrapField($group->get('title'));
    $this->variables['content']['description'] = $group->get('description')->isEmpty() ? NULL : $this->wrapField($group->get('description'));
    $this->variables['content']['website'] = $group->get('website')->isEmpty() ? NULL : $this->wrapField($group->get('website'));
    $this->variables['content']['phone_number'] = $group->get('phone_number')->isEmpty() ? NULL : $this->wrapField($group->get('phone_number'));
    $this->variables['content']['email_address'] = $group->get('email_address')->isEmpty() ? NULL : $this->wrapField($group->get('email_address'));
    $this->variables['content']['location'] = $group->get('location')->isEmpty() ? NULL : $this->wrapField($group->get('location'));
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder->setLimit(self::EVENT_LIST_LIMIT)->render();
    // Add manager links.
    if (AccessControl::isManager($group->organization->entity)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.group.edit_form', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $this->variables['content']['links']['manage_events'] = $this->wrapElement(t('Manage events'), 'manage_events', new Url(
        'entity.group.events', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $this->variables['content']['links']['manage_imports'] = $this->wrapElement(t('Manage imports'), 'manage_imports', new Url(
        'entity.group.imports', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $this->variables['content']['links']['results'] = $this->wrapElement(t('View results'), 'view_results', new Url(
        'entity.group.results', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $publish_state = $group->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.group.publish_form', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
    }
    // Add organizer links.
    elseif (AccessControl::isOrganizer($group)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.group.edit_form', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $this->variables['content']['links']['manage_events'] = $this->wrapElement(t('Manage events'), 'manage_events', new Url(
        'entity.group.events', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
      $this->variables['content']['links']['manage_imports'] = $this->wrapElement(t('Manage imports'), 'manage_imports', new Url(
        'entity.group.imports', [
          'organization' => PathHelper::transliterate($group->organization->entity->label()),
          'group' => PathHelper::transliterate($group->label()),
        ]
      ));
    }
    return $this->variables;
  }

}
