<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating CSV imports.
 *
 * @group activeforanimals
 */
class CSVImportTest extends WebTestBase {

  const ADD_CSV_IMPORT_PATH = '/import/csv';
  const STARTDATE = '12/13/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '12/13/2016';
  const ENDTIME = '13:00';

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
    // Disable user timezones.
    // This is required in order for events to register correct time.
    $systemDate = \Drupal::configFactory()->getEditable('system.date');
    $systemDate->set('timezone.default', 'UTC');
    $systemDate->save(TRUE);
    // Create users and structure.
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $groups = OrganizationHelper::getGroups($this->organization);
    $this->group = array_pop($groups);
    $this->csvfilepath = $this->container->get('file_system')->realpath(drupal_get_path('profile', 'activeforanimals') . '/src/Tests/testdata/sample.csv');
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->organizer);
    $this->drupalGet(self::ADD_CSV_IMPORT_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'parent[0][target_id]' => $this->group->id(),
      'files[field_file_csv_0]' => $this->csvfilepath,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the import.', 'Added a new import entity.');
    $this->assertText('One item imported', 'Successfully imported event');
    // Get imported events.
    $events = GroupHelper::getEvents($this->group);
    $this->assertEqual(count($events), 1, 'Imported one event');
    $event = array_pop($events);
    $event_path = $event->toUrl()->toString();
    $this->drupalGet($event_path);
    $this->assertResponse(200);
    $this->assertText(sprintf('%s - %s', self::STARTDATE, self::STARTTIME), 'Start date and time found.');
    $this->assertText(sprintf('%s - %s', self::ENDDATE, self::ENDTIME), 'End date and time found.');
  }

}
