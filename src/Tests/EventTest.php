<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating events.
 *
 * @group activeforanimals
 */
class EventTest extends WebTestBase {

  const ADD_EVENT_PATH = 'create-event';
  const TITLE = 'Test event';
  const DESCRIPTION = 'Test event description';
  const STARTDATE = '2016-01-01';
  const STARTDATEFORMATTED = '01/01/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '2016-01-01';
  const ENDDATEFORMATTED = '01/01/2016';
  const ENDTIME = '12:00';
  const LOCATION_ADDRESS = '';
  const LOCATION_EXTRA_INFORMATION = 'Test location';

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
   * The organization to host the group.
   *
   * @var Organization
   */
  private $organization;

  /**
   * The group to host the event.
   *
   * @var Group
   */
  private $group;

  /**
   * Container for the manager user.
   *
   * @var User
   */
  private $manager;

  /**
   * Container for the organizer user.
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
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = (new CreateGroup($this->organization, $this->organizer))->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->organizer);
    $this->drupalGet(self::ADD_EVENT_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::TITLE,
      'description[0][value]' => self::DESCRIPTION,
      'start_date[0][value][date]' => self::STARTDATE,
      'start_date[0][value][time]' => self::STARTTIME,
      'end_date[0][value][date]' => self::ENDDATE,
      'end_date[0][value][time]' => self::ENDTIME,
      'parent[0][target_id]' => $this->group->id(),
      'location[0][address]' => self::LOCATION_ADDRESS,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');
    $this->assertText(self::TITLE, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION, 'Set description correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION, 'Set location extra information correctly.');
    $this->assertText(self::STARTDATEFORMATTED, 'Set start date correctly.');
    $this->assertText(self::STARTTIME, 'Set start time correctly.');
    $this->assertText(self::ENDDATEFORMATTED, 'Set end date correctly.');
    $this->assertText(self::ENDTIME, 'Set end time correctly.');
  }
}
