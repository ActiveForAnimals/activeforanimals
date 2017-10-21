<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating CSV exports.
 *
 * @group activeforanimals
 */
class CSVExportTest extends WebTestBase {

  const ADD_CSV_EXPORT_PATH = '/export/csv';
  const RESULTTYPE = 'leafleting';
  const RESULT = 'leafleting | 4 | 0 | 1 | 0 | 1000 | Flyer A';
  const STARTDATE = '12/13/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '12/13/2016';
  const ENDTIME = '13:00';
  const NUMBER_OF_EXPORTED_EVENTS = 2;

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
  }

  /**
   * Run test.
   */
  public function testDo() {
    // Export CSV file.
    $this->drupalLogin($this->organizer);
    $this->drupalGet(self::ADD_CSV_EXPORT_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'parent[0][target_id]' => $this->group->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the export.', 'Added a new export entity.');
    $this->assertText(sprintf('%d items exported.', self::NUMBER_OF_EXPORTED_EVENTS), 'Successfully exported events');
  }

}
