<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Constant;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\user\Entity\User;

/**
 * Creates a test group.
 */
class CreateGroup {

  const TITLE = 'Test group';
  const TIMEZONE = 'inherit';

  private $organization;

  private $timezone;

  private $title;

  /**
   * Constructor.
   *
   * @param \Drupal\effective_activism\Entity\Organization $organization
   *   The organization the group belongs to.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   * @param string $title
   *   Optional title of the group.
   * @param bool $use_default_result_types
   *   If TRUE, adds this group to the organization default result types.
   */
  public function __construct(Organization $organization, User $organizer, $title = NULL, $use_default_result_types = FALSE) {
    $this->organization = $organization;
    $this->organizer = $organizer;
    $this->title = empty($title) ? self::TITLE : $title;
    $this->use_default_result_types = $use_default_result_types;
  }

  /**
   * Create group.
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
    // Activate all default result types for this group.
    if ($this->use_default_result_types === TRUE) {
      foreach (Constant::DEFAULT_RESULT_TYPES as $import_name => $settings) {
        $result_type = ResultTypeHelper::getResultTypeByImportName($import_name, $this->organization->id());
        $result_type->groups = array_merge($result_type->groups, [$group->id() => $group->id()]);
      }
      $result_type->save();
    }
    return $group;
  }

}
