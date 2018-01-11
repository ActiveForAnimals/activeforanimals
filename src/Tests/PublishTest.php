<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateEventTemplate;
use Drupal\activeforanimals\Tests\Helper\CreateFilter;
use Drupal\activeforanimals\Tests\Helper\CreateGroup;
use Drupal\activeforanimals\Tests\Helper\CreateImport;
use Drupal\activeforanimals\Tests\Helper\CreateExport;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Entity\Organization;
use Drupal\simpletest\WebTestBase;

/**
 * Access restriction tests.
 *
 * @group activeforanimals
 */
class PublishTest extends WebTestBase {

  const PATH_SELECT_EVENT_TEMPLATE = '/select-event-template';
  const PATH_EVENT_ADD_FROM_TEMPLATE = '/create-event/1';
  const ADD_CSV_IMPORT_PATH = 'import/csv';
  const ADD_CSV_EXPORT_PATH = 'export/csv';
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
   * @var \Drupal\effective_activism\Entity\Organization
   */
  private $organization;

  /**
   * Container for an event template.
   *
   * @var \Drupal\effective_activism\Entity\EventTemplate
   */
  private $eventtemplate;

  /**
   * Container for a filter.
   *
   * @var \Drupal\effective_activism\Entity\Filter
   */
  private $filter;

  /**
   * Container for a group.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group;

  /**
   * Container for an event.
   *
   * @var \Drupal\effective_activism\Entity\Group
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
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    // Create organizational structure.
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->eventtemplate = (new CreateEventTemplate($this->organization, $this->manager))->execute();
    $this->filter = (new CreateFilter($this->organization, $this->manager))->execute();
    $this->group = (new CreateGroup($this->organization, $this->organizer, NULL, TRUE))->execute();
    $this->event = (new CreateEvent($this->group, $this->organizer))->execute();
    $this->csvfilepath = $this->container->get('file_system')->realpath(drupal_get_path('profile', 'activeforanimals') . '/src/Tests/testdata/sample.csv');
    $this->import = (new CreateImport($this->group, $this->organizer, $this->csvfilepath))->execute();
    $this->export = (new CreateExport($this->organization, $this->filter, $this->manager))->execute();
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
    // User has access to event template selection form.
    $this->drupalGet(self::PATH_SELECT_EVENT_TEMPLATE);
    $this->assertResponse(200);
    // User has access to event form with template selected.
    $this->drupalGet(self::PATH_EVENT_ADD_FROM_TEMPLATE);
    $this->assertResponse(200);
    $this->assertText(CreateEventTemplate::EVENT_DESCRIPTION);
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
    $this->assertText('8 items unpublished.');

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
    $this->assertText('8 items published.');
    $this->drupalGet(sprintf('%s/publish', $this->organization->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('13 items unpublished.');

    // Verify that organizer cannot access organization, group and event.
    $this->drupalLogin($this->organizer);
    // User does not have access to organization page.
    $this->drupalGet($this->organization->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to group page.
    $this->drupalGet($this->group->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet($this->event->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to import page.
    $this->drupalGet($this->import->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet(sprintf('%s/e/2', $this->group->toUrl()->toString()));
    $this->assertResponse(403);
    // User does not have access to export page.
    $this->drupalGet($this->export->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to filter page.
    $this->drupalGet($this->filter->toUrl()->toString());
    $this->assertResponse(403);
    // User does not have access to event template page.
    $this->drupalGet($this->eventtemplate->toUrl()->toString());
    $this->assertResponse(403);
    // User has access to event form with template selected but template is
    // not added.
    $this->drupalGet(self::PATH_EVENT_ADD_FROM_TEMPLATE);
    $this->assertResponse(200);
    $this->assertNoText(CreateEventTemplate::EVENT_TITLE);
    $this->assertNoText(CreateEventTemplate::EVENT_DESCRIPTION);

    // Publish import and events.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf('%s/publish', $this->import->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('6 items published.');

    // Verify that organizer can access import and events.
    $this->drupalLogin($this->organizer);
    // User has access to import page.
    $this->drupalGet($this->import->toUrl()->toString());
    $this->assertResponse(200);
    // User has access to event page.
    $this->drupalGet(sprintf('%s/e/2', $this->group->toUrl()->toString()));
    $this->assertResponse(200);

    // Publish export.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf('%s/publish', $this->export->toUrl()->toString()));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');

    // Verify that organizer can access export.
    $this->drupalLogin($this->organizer);
    $this->drupalGet($this->export->toUrl()->toString());
    $this->assertResponse(200);
  }

}
