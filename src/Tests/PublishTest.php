<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Organization;
use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\User;

/**
 * Access restriction tests.
 *
 * @group activeforanimals
 */
class PublishTest extends WebTestBase {

  const PATH_EVENT_ADD = 'create-event';
  const ADD_CSV_IMPORT_PATH = 'import/csv';
  const GROUP_TITLE_1 = 'Test group 1';
  const GROUP_TITLE_1_MODIFIED = 'Test group 1 (updated)';
  const GROUP_TITLE_2 = 'Test group 2';
  const GROUP_TITLE_2_MODIFIED = 'Test group 2 (updated)';
  const STARTDATE = '2016-01-01';
  const STARTTIME = '11:00';
  const ENDDATE = '2016-01-01';
  const ENDTIME = '12:00';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'activeforanimals';

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'effective_activism',
  ];

  /**
   * Container for an organization.
   *
   * @var Organization
   */
  private $organization;

  /**
   * Container for a group.
   *
   * @var Group
   */
  private $group;

  /**
   * Container for an event.
   *
   * @var Group
   */
  private $event;

  /**
   * Container for a manager user.
   *
   * @var User
   */
  private $manager;

  /**
   * Container for an organizer user.
   *
   * @var User
   */
  private $organizer;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    // Create organizational structure.
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = (new CreateGroup($this->organization, $this->organizer))->execute();
    $this->event = (new CreateEvent($this->group, $this->organizer))->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    // Verify that organizer can access all parts of organization.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet($this->organization->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to group page.
    $this->drupalGet($this->group->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to event page.
    $this->drupalGet($this->event->toUrl()->toString());
    $this->assertResponse(200);

    // Unpublish event.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf('%s/publish', $this->event->toUrl()->toString()));
    // User may create event.
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('One item unpublished.');

    // Verify that organizer cannot access event.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet($this->organization->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to group page.
    $this->drupalGet($this->group->toUrl()->toString());
    $this->assertResponse(200);
    // User does not have access to event page.
    $this->drupalGet($this->event->toUrl()->toString());
    $this->assertResponse(403);

    // Publish event and unpublish group.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf('%s/publish', $this->event->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');
    $this->drupalGet(sprintf('%s/publish', $this->group->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('2 items unpublished.');

    // Verify that organizer cannot access group and event.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet($this->organization->toUrl()->toString());
    $this->assertResponse(200);
    // User does not have access to group page.
    $this->drupalGet($this->group->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet($this->event->toUrl()->toString());
    $this->assertResponse(403);

    // Publish event and group and unpublish organization.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf('%s/publish', $this->event->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');
    $this->drupalGet(sprintf('%s/publish', $this->group->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('2 items published.');
    $this->drupalGet(sprintf('%s/publish', $this->organization->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('4 items unpublished.');

    // Verify that organizer cannot access organization, group and event.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet($this->organization->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to group page.
    $this->drupalGet($this->group->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet($this->event->toUrl()->toString());
    $this->assertResponse(403);
  }

}
