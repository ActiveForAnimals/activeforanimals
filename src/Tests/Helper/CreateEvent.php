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

  private $group;

  private $timezone;

  private $title;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Group $group
   *   The group the event belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   * @param string $title
   *   Optional title of the group.
   */
  public function __construct(Group $group, User $organizer, $title = NULL) {
    $this->group = $group;
    $this->organizer = $organizer;
    $this->title = empty($title) ? self::TITLE : $title;
  }

  /**
   * Create event.
   */
  public function execute() {
    $event = Event::create([
      'user_id' => $this->organizer->id(),
      'title' => $this->title,
      'parent' => $this->group->id(),
    ]);
    $event->save();
    return $event;
  }

}
