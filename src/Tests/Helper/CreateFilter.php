<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal\effective_activism\Entity\Filter;
use Drupal\effective_activism\Entity\Organization;
use Drupal\user\Entity\User;

/**
 * Creates a test filter.
 */
class CreateFilter {

  const TITLE = 'Test filter';

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
   *   The organization the filter belongs to.
   * @param \Drupal\user\Entity\User $manager
   *   The manager of the organization.
   * @param string $title
   *   Optional title of the filter.
   */
  public function __construct(Organization $organization, User $manager, $title = NULL) {
    $this->organization = $organization;
    $this->manager = $manager;
    $this->title = empty($title) ? self::TITLE : $title;
  }

  /**
   * Create filter.
   */
  public function execute() {
    $filter = Filter::create([
      'user_id' => $this->manager->id(),
      'name' => $this->title,
      'organization' => $this->organization->id(),
    ]);
    $filter->save();
    return $filter;
  }

}
