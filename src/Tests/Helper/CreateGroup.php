<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Organization;
use Drupal\user\Entity\User;

class CreateGroup {

  const TITLE = 'Test group';
  const TIMEZONE = 'inherit';

  private $organization;

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
  public function __construct(Organization $organization, User $organizer, $title = NULL) {
    $this->organization = $organization;
    $this->organizer = $organizer;
    $this->title = empty($title) ? self::TITLE : $title;
  }

  /**
   * {inheritdoc}
   */
  public function execute() {
    $group = Group::create([
      'user_id' => $this->organizer->id(),
      'title' => $this->title,
      'timezone' => self::TIMEZONE,
      'organizers' => $this->organizer->id(),
      'organization' => $this->organization->id(),
    ]);
    $group->save();
    return $group;
  }
}
