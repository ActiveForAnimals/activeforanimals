<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Organization;
use Drupal\user\Entity\User;

/**
 * Creates a test result type.
 */
class CreateResultType {

  const TITLE = 'Test group';
  const TIMEZONE = 'inherit';

  private $organization;

  private $timezone;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Organization $organization
   *   The organization the group belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   */
  public function __construct(Organization $organization, User $organizer) {
    $this->organization = $organization;
    $this->organizer = $organizer;
  }

  /**
   * Create result type.
   */
  public function execute() {
    $group = Group::create([
      'user_id' => $this->organizer->id(),
      'title' => self::TITLE,
      'timezone' => self::TIMEZONE,
      'organizers' => $this->organizer->id(),
      'organization' => $this->organization->id(),
    ]);
    $group->save();
    return $group;
  }

}
