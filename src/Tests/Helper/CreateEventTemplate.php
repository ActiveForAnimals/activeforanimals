<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\EventTemplate;
use Drupal\effective_activism\Entity\Organization;
use Drupal\user\Entity\User;

/**
 * Creates a test event template.
 */
class CreateEventTemplate {

  const TITLE = 'Test event template';
  const EVENT_TITLE = 'A sample event title';
  const EVENT_DESCRIPTION = 'A sample event description';

  /**
   * Organization.
   *
   * @var \Drupal\effective_activism\Entity\Organization
   */
  private $organization;

  /**
   * Manager.
   *
   * @var \Drupal\user\Entity\User
   */
  private $manager;

  /**
   * Title.
   *
   * @var string|null
   */
  private $title;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Organization $organization
   *   The organization the event template belongs to.
   * @param \Drupal\user\Entity\User $manager
   *   The manager of the organization.
   * @param string $title
   *   Optional title of the event template.
   */
  public function __construct(Organization $organization, User $manager, $title = NULL) {
    $this->organization = $organization;
    $this->manager = $manager;
    $this->title = empty($title) ? self::TITLE : $title;
  }

  /**
   * Create event template.
   */
  public function execute() {
    $event_template = EventTemplate::create([
      'user_id' => $this->manager->id(),
      'name' => $this->title,
      'organization' => $this->organization->id(),
      'event_title' => self::EVENT_TITLE,
      'event_description' => self::EVENT_DESCRIPTION,
    ]);
    $event_template->save();
    return $event_template;
  }

}
