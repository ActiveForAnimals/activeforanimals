<?php

namespace Drupal\activeforanimals\Tests\Helper;

use Drupal;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Constant;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\ResultType;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\user\Entity\User;

/**
 * Creates a test organization.
 */
class CreateOrganization {

  const TITLE = 'Test organization';

  /**
   * Manager.
   *
   * @var \Drupal\user\Entity\User
   */
  private $manager;

  /**
   * Organizer.
   *
   * @var \Drupal\user\Entity\User
   */
  private $organizer;

  /**
   * Title.
   *
   * @var string|null
   */
  private $title;

  /**
   * Timezone.
   *
   * @var array|mixed|null
   */
  private $timezone;

  /**
   * Constructor.
   *
   * @param \Drupal\user\Entity\User $manager
   *   The manager of the organization.
   * @param \Drupal\user\Entity\User $organizer
   *   The organizer of the group.
   * @param string $title
   *   An optional title for the organization.
   */
  public function __construct(User $manager, User $organizer, $title = NULL) {
    $this->manager = $manager;
    $this->organizer = $organizer;
    $this->title = empty($title) ? self::TITLE : $title;
    $this->timezone = Drupal::config('system.date')->get('timezone.default');
  }

  /**
   * Create organization.
   */
  public function execute() {
    $organization = Organization::create([
      'user_id' => $this->manager->id(),
      'title' => $this->title,
      'timezone' => $this->timezone,
      'managers' => $this->manager->id(),
    ]);
    $organization->save();
    // Create tagging vocabulary for organization.
    $name = t('@organization tags', ['@organization' => $organization->label()]);
    $vid = sprintf('tags_%d', $organization->id());
    $vocabulary = Vocabulary::create([
      'vid' => $vid,
      'name' => $name,
    ]);
    $vocabulary->save();
    // Create a group when creating an organization.
    $group = Group::create([
      'title' => Constant::GROUP_DEFAULT_VALUES['title'],
      'organization' => $organization->id(),
      'organizers' => $this->organizer->id(),
    ]);
    $group->save();
    // Create default result types for new organizations.
    foreach (Constant::DEFAULT_RESULT_TYPES as $import_name => $settings) {
      $result_type = ResultType::create([
        'id' => ResultTypeHelper::getUniqueId($import_name),
        'label' => $settings['label'],
        'importname' => $import_name,
        'description' => $settings['description'],
        'datatypes' => $settings['datatypes'],
        'organization' => $organization->id(),
        'groups' => [
          $group->id() => $group->id(),
        ],
      ]);
      if ($result_type->save() === SAVED_NEW) {
        ResultTypeHelper::updateBundleSettings($result_type);
        ResultTypeHelper::addTaxonomyField($result_type);
      }
    }
    return $organization;
  }

}
