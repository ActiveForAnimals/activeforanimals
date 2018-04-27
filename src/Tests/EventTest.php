<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating events.
 *
 * @group activeforanimals
 */
class EventTest extends WebTestBase {

  const ADD_EVENT_PATH = '/o/%s/g/%s/e/add';
  const TITLE = 'Test event';
  const DESCRIPTION = 'Test event description';
  const STARTDATE = '2016-01-01 11:00';
  const STARTDATEFORMATTED = '2016-01-01 11:00';
  const ENDDATE = '2016-01-01 12:00';
  const ENDDATEFORMATTED = '2016-01-01 12:00';
  const LOCATION_ADDRESS = 'Copenhagen, Denmark';
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
    // Disable user time zones.
    // This is required in order for events to register correct time.
    $systemDate = Drupal::configFactory()->getEditable('system.date');
    $systemDate->set('timezone.default', 'UTC');
    $systemDate->save(TRUE);
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
    $this->drupalGet(sprintf(
      self::ADD_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::TITLE,
      'description[0][value]' => self::DESCRIPTION,
      'start_date[0][value]' => self::STARTDATE,
      'end_date[0][value]' => self::ENDDATE,
      'location[0][address]' => self::LOCATION_ADDRESS,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');
    $this->assertText(self::TITLE, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION, 'Set description correctly.');
    $this->assertText(self::LOCATION_ADDRESS, 'Set location address correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION, 'Set location extra information correctly.');
    $this->assertText(self::STARTDATEFORMATTED, 'Set start date correctly.');
    $this->assertText(self::ENDDATEFORMATTED, 'Set end date correctly.');
  }

}
