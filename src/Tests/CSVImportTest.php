<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating CSV imports.
 *
 * @group activeforanimals
 */
class CSVImportTest extends WebTestBase {

  const ADD_CSV_IMPORT_PATH = '/import/csv';
  const RESULTTYPE = 'leafleting';
  const RESULT = 'leafleting | 4 | 0 | 1 | 0 | 1000 | Flyer A';
  const STARTDATE = '12/13/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '12/13/2016';
  const ENDTIME = '13:00';
  const NUMBER_OF_IMPORTED_EVENTS = 2;

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
    $systemDate = Drupal::configFactory()->getEditable('system.date');
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
    // Import CSV file.
    $this->drupalLogin($this->organizer);
    $this->drupalGet(self::ADD_CSV_IMPORT_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'parent[0][target_id]' => $this->group->id(),
      'files[field_file_csv_0]' => $this->csvfilepath,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the import.', 'Added a new import entity.');
    $this->assertText('3 items imported.', 'Successfully imported event');
    // Get imported events.
    $events = GroupHelper::getEvents($this->group);
    $this->assertEqual(count($events), self::NUMBER_OF_IMPORTED_EVENTS, 'Imported two events');
    $event = array_shift($events);
    $event_path = $event->toUrl()->toString();
    $this->drupalGet($event_path);
    $this->assertResponse(200);
    $this->assertText(sprintf('%s - %s', self::STARTDATE, self::STARTTIME), 'Start date and time found.');
    $this->assertText(sprintf('%s - %s', self::ENDDATE, self::ENDTIME), 'End date and time found.');
    // Remove result type from group.
    $result_type = ResultTypeHelper::getResultTypeByImportName(self::RESULTTYPE, $this->organization->id());
    $result_type->groups = [];
    $result_type->save();
    // Fail CSV import.
    $this->drupalGet(self::ADD_CSV_IMPORT_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'parent[0][target_id]' => $this->group->id(),
      'files[field_file_csv_0]' => $this->csvfilepath,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('The CSV file contains a row with an incorrect result (%s)', self::RESULT), 'Failed to import.');
  }

}
