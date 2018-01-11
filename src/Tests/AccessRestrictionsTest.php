<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateEventTemplate;
use Drupal\activeforanimals\Tests\Helper\CreateFilter;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Helper\ResultTypeHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Access restriction tests.
 *
 * @group activeforanimals
 */
class AccessRestrictionsTest extends WebTestBase {

  const PATH_EVENT_ADD = 'create-event';
  const PATH_ADD_CSV_IMPORT = 'import/csv';
  const PATH_SELECT_EVENT_TEMPLATE = '/select-event-template';
  const RESULT_TYPE_1 = 'leafleting';
  const RESULT_TYPE_2 = 'pay_per_view_event';
  const EVENT_TEMPLATE_TITLE_1 = 'Test event template 1';
  const EVENT_TEMPLATE_TITLE_2 = 'Test event template 2';
  const FILTER_TITLE_1 = 'Test filter 1';
  const FILTER_TITLE_2 = 'Test filter 2';
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
   * Container for the organization1 organization.
   *
   * @var Organization
   */
  private $organization1;

  /**
   * Container for the organization2 organization.
   *
   * @var Organization
   */
  private $organization2;

  /**
   * Container for the eventtemplate1 event template.
   *
   * @var EventTemplate
   */
  private $eventtemplate1;

  /**
   * Container for the eventtemplate2 event template.
   *
   * @var EventTemplate
   */
  private $eventtemplate2;

  /**
   * Container for the filter1 filter.
   *
   * @var Filter
   */
  private $filter1;

  /**
   * Container for the filter2 filter.
   *
   * @var Filter
   */
  private $filter2;

  /**
   * Container for the group1 group.
   *
   * @var Group
   */
  private $group1;

  /**
   * Container for the group2 group.
   *
   * @var Group
   */
  private $group2;

  /**
   * Container for the manager1 user.
   *
   * @var User
   */
  private $manager1;

  /**
   * Container for the manager2 user.
   *
   * @var User
   */
  private $manager2;

  /**
   * Container for the organizer1 user.
   *
   * @var User
   */
  private $organizer1;

  /**
   * Container for the organizer2 user.
   *
   * @var User
   */
  private $organizer2;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager1 = $this->drupalCreateUser();
    $this->manager2 = $this->drupalCreateUser();
    $this->organizer1 = $this->drupalCreateUser();
    $this->organizer2 = $this->drupalCreateUser();
    // Create organizational structure.
    $this->organization1 = (new CreateOrganization($this->manager1, $this->organizer1))->execute();
    $this->organization2 = (new CreateOrganization($this->manager2, $this->organizer2))->execute();
    $this->filter1 = (new CreateFilter($this->organization1, $this->manager1, self::FILTER_TITLE_1))->execute();
    $this->filter2 = (new CreateFilter($this->organization2, $this->manager2, self::FILTER_TITLE_2))->execute();
    $this->eventtemplate1 = (new CreateEventTemplate($this->organization1, $this->manager1, self::EVENT_TEMPLATE_TITLE_1))->execute();
    $this->eventtemplate2 = (new CreateEventTemplate($this->organization2, $this->manager2, self::EVENT_TEMPLATE_TITLE_2))->execute();
    $this->group1 = (new CreateGroup($this->organization1, $this->organizer1, self::GROUP_TITLE_1))->execute();
    $this->group2 = (new CreateGroup($this->organization2, $this->organizer2, self::GROUP_TITLE_2))->execute();
    // Add leafleting result type to group1.
    $result_type = ResultTypeHelper::getResultTypeByImportName(self::RESULT_TYPE_1, $this->organization1->id());
    $result_type->groups = [$this->group1->id()];
    $result_type->save();
    // Add pay-per-view result type to group1.
    $result_type = ResultTypeHelper::getResultTypeByImportName(self::RESULT_TYPE_2, $this->organization1->id());
    $result_type->groups = [$this->group1->id()];
    $result_type->save();
  }

  /**
   * Run test.
   */
  public function testDo() {
    // Verify that manager1 can manage filter1 and not filter2.
    $this->drupalLogin($this->manager1);
    $this->drupalGet($this->filter1->toUrl()->toString());
    $this->assertResponse(200);
    $this->drupalGet($this->filter2->toUrl()->toString());
    $this->assertResponse(403);
    // Verify that manager1 can manage eventtemplate1 and not eventtemplate2.
    $this->drupalLogin($this->manager1);
    $this->drupalGet($this->eventtemplate1->toUrl()->toString());
    $this->assertResponse(200);
    $this->drupalGet($this->eventtemplate2->toUrl()->toString());
    $this->assertResponse(403);

    // Verify that organizer1 can use eventtemplate1 and not eventtemplate2.
    $this->drupalLogin($this->organizer1);
    $this->drupalGet(self::PATH_SELECT_EVENT_TEMPLATE);
    $this->assertText(self::EVENT_TEMPLATE_TITLE_1);
    $this->assertNoText(self::EVENT_TEMPLATE_TITLE_2);
    $this->drupalPostForm(NULL, [
      'organization' => $this->organization1->id(),
      'event_template' => $this->eventtemplate1->id(),
    ], t('Select'));
    $this->assertResponse(200);

    // Verify that manager1 can manage group1 and not group2.
    $this->drupalLogin($this->manager1);
    $this->drupalGet(sprintf('%s/g', $this->organization1->toUrl()->toString()));
    $this->assertResponse(200);
    // User has access to group.
    $this->drupalGet($this->group1->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to group edit page.
    $this->drupalGet(sprintf('%s/edit', $this->group1->toUrl()->toString()));
    $this->assertResponse(200);
    // User may make changes to group.
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::GROUP_TITLE_1_MODIFIED,
      'phone_number[0][value]' => '',
      'email_address[0][value]' => '',
      'location[0][address]' => '',
      'location[0][extra_information]' => '',
      'timezone' => Drupal::config('system.date')->get('timezone.default'),
      'description[0][value]' => '',
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Saved the %s group.', self::GROUP_TITLE_1_MODIFIED), 'Changed title of the group.');

    // User doesn't have access to group page.
    $this->drupalGet($this->group2->toUrl()->toString());
    $this->assertResponse(403);
    // User doesn't have access to group edit page.
    $this->drupalGet(sprintf('%s/edit', $this->group2->toUrl()->toString()));
    $this->assertResponse(403);

    // Verify that manager1 can create events for group1.
    $this->drupalLogin($this->manager1);
    // User has access to event overview page.
    $this->drupalGet(sprintf('%s/e', $this->group1->toUrl()->toString()));
    $this->assertResponse(200);
    // User has access to event add page.
    $this->drupalGet(self::PATH_EVENT_ADD);
    $this->assertResponse(200);
    // User may create event.
    $this->drupalPostForm(NULL, [
      'title[0][value]' => '',
      'description[0][value]' => '',
      'start_date[0][value][date]' => self::STARTDATE,
      'start_date[0][value][time]' => self::STARTTIME,
      'end_date[0][value][date]' => self::ENDDATE,
      'end_date[0][value][time]' => self::ENDTIME,
      'parent[0][target_id]' => $this->group1->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');

    // Verify that organizer1 can create events for group1.
    $this->drupalLogin($this->organizer1);
    // User has access to event overview page.
    $this->drupalGet(sprintf('%s/e', $this->group1->toUrl()->toString()));
    $this->assertResponse(200);
    // User has access to event add page.
    $this->drupalGet(self::PATH_EVENT_ADD);
    $this->assertResponse(200);
    // User may create event.
    $this->drupalPostForm(NULL, [
      'title[0][value]' => '',
      'description[0][value]' => '',
      'start_date[0][value][date]' => self::STARTDATE,
      'start_date[0][value][time]' => self::STARTTIME,
      'end_date[0][value][date]' => self::ENDDATE,
      'end_date[0][value][time]' => self::ENDTIME,
      'parent[0][target_id]' => $this->group1->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');

    // Verify that organizer2 cannot manage events from group1.
    $this->drupalLogin($this->organizer2);
    // User cannot create events with group.
    $this->drupalGet(self::PATH_EVENT_ADD);
    $this->assertResponse(200);
    $this->assertNoText($this->group1->get('title')->value, 'User does not have access to group on event creation pages.');
    // User cannot see events belonging to other groups.
    $this->drupalGet(sprintf('%s/e', $this->group1->toUrl()->toString()));
    $this->assertResponse(403);
    // User has no access to event page.
    $this->drupalGet(sprintf('%s/e/1', $this->group1->toUrl()->toString()));
    $this->assertResponse(403);
    // User has no access to event edit page.
    $this->drupalGet(sprintf('%s/e/1/edit', $this->group1->toUrl()->toString()));
    $this->assertResponse(403);

    // Add manager1 to organization2.
    $this->organization2->managers->appendItem($this->manager1->id());
    $this->organization2->save();

    // Verify that manager1 can manage group2.
    $this->drupalLogin($this->manager1);
    // User has access to group overview page.
    $this->drupalGet(sprintf('%s/g', $this->organization2->toUrl()->toString()));
    $this->assertResponse(200);
    // User has access to group.
    $this->drupalGet($this->group2->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to group edit page.
    $this->drupalGet(sprintf('%s/edit', $this->group2->toUrl()->toString()));
    $this->assertResponse(200);
    // User may make changes to group.
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::GROUP_TITLE_2_MODIFIED,
      'phone_number[0][value]' => '',
      'email_address[0][value]' => '',
      'location[0][address]' => '',
      'location[0][extra_information]' => '',
      'timezone' => Drupal::config('system.date')->get('timezone.default'),
      'description[0][value]' => '',
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Saved the %s group.', self::GROUP_TITLE_2_MODIFIED), 'Changed title of the group.');

    // Verify that manager1 can create events for group2.
    $this->drupalGet(sprintf('%s/e', $this->group2->toUrl()->toString()));
    $this->assertResponse(200);
    // User has access to event add page.
    $this->drupalGet(self::PATH_EVENT_ADD);
    $this->assertResponse(200);
    // User may create event.
    $this->drupalPostForm(NULL, [
      'title[0][value]' => '',
      'description[0][value]' => '',
      'start_date[0][value][date]' => self::STARTDATE,
      'start_date[0][value][time]' => self::STARTTIME,
      'end_date[0][value][date]' => self::ENDDATE,
      'end_date[0][value][time]' => self::ENDTIME,
      'parent[0][target_id]' => $this->group2->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');

    // Import event.
    $this->drupalGet(self::PATH_ADD_CSV_IMPORT);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'parent[0][target_id]' => $this->group1->id(),
      'files[field_file_csv_0]' => $this->container->get('file_system')->realpath(drupal_get_path('profile', 'activeforanimals') . '/src/Tests/testdata/sample.csv'),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created the import.', 'Added a new import entity.');
    $this->assertText('2 items imported.', 'Successfully imported event');

    // Verify that manager2 cannot manage import.
    $this->drupalLogin($this->manager2);
    $this->drupalGet(sprintf('%s/imports/1', $this->group1->toUrl()->toString()));
    $this->assertResponse(403);
  }

}
