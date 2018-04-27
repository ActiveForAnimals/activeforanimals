<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating iCalendar imports.
 *
 * @group activeforanimals
 */
class ICalendarImportTest extends WebTestBase {

  const ICALENDAR_PATH = 'https://raw.githubusercontent.com/ActiveForAnimals/activeforanimals/master/src/Tests/testdata/sample.ics';
  const ADD_ICALENDAR_IMPORT_PATH = '/o/%s/g/%s/imports/add/icalendar';
  const EVENT_PATH = '/o/%s/g/%s/e/%d';
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
   * The test organization.
   *
   * @var Organization
   */
  private $organization;

  /**
   * The test group.
   *
   * @var Group
   */
  private $group;

  /**
   * The test manager.
   *
   * @var User
   */
  private $manager;

  /**
   * The test organizer.
   *
   * @var User
   */
  private $organizer;

  /**
   * The path to the CSV test file.
   *
   * @var string
   */
  private $csvfilepath;

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
    // Create users and structure.
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $groups = OrganizationHelper::getGroups($this->organization);
    $this->group = array_pop($groups);
  }

  /**
   * Run test.
   */
  public function testDo() {
    // Import iCalendar file.
    $this->drupalLogin($this->organizer);
    $this->drupalGet(sprintf(
      self::ADD_ICALENDAR_IMPORT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'field_url[0][uri]' => self::ICALENDAR_PATH,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the import.', 'Added a new import entity.');
    $this->assertText('One item imported', 'Successfully imported event');
    $this->drupalGet(sprintf(
      self::EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      1
    ));
    $this->assertResponse(200);
    $this->assertText(sprintf('%s %s', self::STARTDATE, self::STARTTIME), 'Start date and time found.');
    $this->assertText(sprintf('%s %s', self::ENDDATE, self::ENDTIME), 'End date and time found.');
  }

}
