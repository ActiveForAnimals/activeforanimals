<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\Event;
use Drupal\effective_activism\Entity\Group;
use Drupal\user\Entity\User;

/**
 * Creates a test event.
 */
class CreateEvent {

  const TITLE = 'Test event';

  private $timezone;

  private $values;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Group $group
   *   The group the event belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   * @param string $title
   *   Optional title of the group.
   * @param array $values
   *   Additional values for event.
   */
  public function __construct(Group $group, User $organizer, $title = NULL, array $values = []) {
    $this->values = array_merge([
      'title' => empty($title) ? self::TITLE : $title,
      'user_id' => $organizer,
      'parent' => $group->id(),
    ], $values);
  }

  /**
   * Create event.
   */
  public function execute() {
    $event = Event::create($this->values);
    $event->save();
    return $event;
  }

}
