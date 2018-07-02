<?php

namespace Drupal\tofu\Preprocessor;

use Drupal\Core\Url;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\ListBuilder\EventListBuilder;
use Drupal\effective_activism\ListBuilder\GroupListBuilder;
use Drupal\effective_activism\Helper\DateHelper;
use Drupal\effective_activism\Helper\PathHelper;

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
    $organization = $this->variables['elements']['#organization'];
    $this->variables['content']['title'] = $this->wrapField($organization->get('title'));
    $this->variables['content']['description'] = $this->wrapField($organization->get('description'));
    $this->variables['content']['logo'] = !$organization->get('logo')->isEmpty() ? $this->wrapImage(
      $organization->get('logo')->entity->getFileUri(),
      'logo',
      self::LOGO_200X200,
      new Url('entity.organization.canonical', ['organization' => PathHelper::transliterate($organization->label())])
    ) : NULL;
    $this->variables['content']['website'] = $organization->get('website')->isEmpty() ? NULL : $this->wrapField($organization->get('website'));
    $this->variables['content']['phone_number'] = $organization->get('phone_number')->isEmpty() ? NULL : $this->wrapField($organization->get('phone_number'));
    $this->variables['content']['email_address'] = $organization->get('email_address')->isEmpty() ? NULL : $this->wrapField($organization->get('email_address'));
    $this->variables['content']['location'] = $organization->get('location')->isEmpty() ? NULL : $this->wrapField($organization->get('location'));
    $this->variables['content']['groups'] = $this->groupListBuilder->render();
    $this->variables['content']['events'] = $this->eventListBuilder
      ->setLimit(self::EVENT_LIST_LIMIT)
      ->setSortAsc(TRUE)
      ->setTitle('Upcoming events')
      ->setFromDate(DateHelper::getNow($organization))
      ->setEmpty('No groups have upcoming events')
      ->setPagerIndex(1)
      ->render();
    // Add manager links.
    if (AccessControl::isManager($organization)->isAllowed()) {
      $this->variables['content']['links']['edit_this_page'] = $this->wrapElement(t('Edit this page'), 'edit_page', new Url(
        'entity.organization.edit_form', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_groups'] = $this->wrapElement(t('Manage groups'), 'manage_groups', new Url(
        'entity.organization.groups', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['results'] = $this->wrapElement(t('View results'), 'view_results', new Url(
        'entity.organization.results', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_results'] = $this->wrapElement(t('Manage results'), 'manage_results', new Url(
        'entity.organization.result_types', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_exports'] = $this->wrapElement(t('Manage exports'), 'manage_exports', new Url(
        'entity.organization.exports', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_filters'] = $this->wrapElement(t('Manage filters'), 'manage_filters', new Url(
        'entity.organization.filters', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_event_templates'] = $this->wrapElement(t('Manage event templates'), 'manage_event_templates', new Url(
        'entity.organization.event_templates', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $this->variables['content']['links']['manage_terms'] = $this->wrapElement(t('Manage tags'), 'manage_terms', new Url(
        'entity.organization.terms', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
      $publish_state = $organization->isPublished() ? t('Unpublish') : t('Publish');
      $this->variables['content']['links']['publish'] = $this->wrapElement($publish_state, 'publish', new Url(
        'entity.organization.publish_form', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
    }
    // Add organizer links.
    elseif (AccessControl::isStaff($organization)->isAllowed()) {
      $this->variables['content']['links']['manage_groups'] = $this->wrapElement(t('Manage groups'), 'manage_groups', new Url(
        'entity.organization.groups', [
          'organization' => PathHelper::transliterate($organization->label()),
        ]
      ));
    }
    return $this->variables;
  }

}
