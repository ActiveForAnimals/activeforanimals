<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Data;
use Drupal\effective_activism\Entity\Export;
use Drupal\effective_activism\Entity\Result;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating CSV exports.
 *
 * @group activeforanimals
 */
class CSVExportTest extends WebTestBase {

  const ADD_CSV_EXPORT_PATH = '/export/csv';
  const TEST_TITLE_1 = 'test 1';
  const TEST_TITLE_2 = 'test 2';
  const STARTDATE = '12/13/2016';
  const STARTTIME = '11:00';
  const ENDDATE = '12/13/2016';
  const ENDTIME = '13:00';
  const NUMBER_OF_EXPORTED_EVENTS = 2;
  const TEST_RESULT_1 = 111111111;
  const TEST_RESULT_2 = 222222222;
  const RESULT = [
    'participant_count' => 1,
    'duration_minutes' => 0,
    'duration_hours' => 1,
    'duration_days' => 0,
  ];
  const DATA_1 = [
    'type' => 'leaflets',
    'field_leaflets' => self::TEST_RESULT_1,
  ];
  const DATA_2 = [
    'type' => 'signatures',
    'field_signatures' => self::TEST_RESULT_2,
  ];


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
   * The 1st test event.
   *
   * @var Event
   */
  private $event1;

  /**
   * The 2nd test event.
   *
   * @var Event
   */
  private $event2;

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
    $this->event1 = (new CreateEvent($this->group, $this->organizer, self::TEST_TITLE_1))->execute();
    $this->event2 = (new CreateEvent($this->group, $this->organizer, self::TEST_TITLE_2))->execute();
    // Get leafleting result type for the organization.
    $leafleting_result_type = ResultTypeHelper::getResultTypeByImportName('leafleting', $this->organization->id());
    $signature_collection_result_type = ResultTypeHelper::getResultTypeByImportName('signature_collection', $this->organization->id());
    // Add one result to event1.
    $data1 = Data::create(self::DATA_1);
    $data1->save();
    $result1_array = array_merge(self::RESULT, [
      'type' => $leafleting_result_type->id(),
      'data_leaflets' => [
        'target_id' => $data1->id(),
      ],
    ]);
    $result1 = Result::create($result1_array);
    $result1->save();
    $this->event1->results[] = [
      'target_id' => $result1->id(),
    ];
    // Add another result to event1.
    $data2 = Data::create(self::DATA_2);
    $data2->save();
    $result2_array = array_merge(self::RESULT, [
      'type' => $signature_collection_result_type->id(),
      'data_signatures' => [
        'target_id' => $data2->id(),
      ],
    ]);
    $result2 = Result::create($result2_array);
    $result2->save();
    $this->event1->results[] = [
      'target_id' => $result2->id(),
    ];
    $this->event1->save();
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
    $this->assertText(sprintf('%d events exported.', self::NUMBER_OF_EXPORTED_EVENTS), 'Successfully exported events');
    // Examine file content.
    $export = Export::load(1);
    $file = $export->field_file_csv->entity;
    $filepath = drupal_realpath($file->getFileUri());
    $handle = fopen($filepath, 'r');
    $content = fread($handle, filesize($filepath));
    fclose($handle);
    $this->assertTrue(strpos($content, self::TEST_TITLE_1), 'Test event 1 found');
    $this->assertTrue(strpos($content, self::TEST_TITLE_2), 'Test event 2 found');
    $this->assertTrue(strpos($content, (string) self::TEST_RESULT_1), 'Test result 1 found');
    $this->assertTrue(strpos($content, (string) self::TEST_RESULT_2), 'Test result 2 found');
  }

}
