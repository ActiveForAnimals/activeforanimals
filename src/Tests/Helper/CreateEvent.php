<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\Event;
use Drupal\effective_activism\Entity\Group;
use Drupal\user\Entity\User;

class CreateEvent {

  const TITLE = 'Test event';

  private $group;

  private $timezone;

  private $title;

  /**
   * Constructor.
   *
   * @param Organization $organization
   *   The organization the group belongs to.
   * @param User $organizer
   *   The organizer of the group.
   * @param string $title
   *   Optional title of the group.
   *
   * @return CreateGroup
   *   An instance of this class.
   */
  public function __construct(Group $group, User $organizer, $title = NULL) {
    $this->group = $group;
    $this->organizer = $organizer;
    $this->title = empty($title) ? self::TITLE : $title;
  }

  /**
   * {inheritdoc}
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
