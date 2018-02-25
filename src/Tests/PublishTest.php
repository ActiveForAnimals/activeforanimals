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
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Access restriction tests.
 *
 * @group activeforanimals
 */
class PublishTest extends WebTestBase {

  const PATH_SELECT_EVENT_TEMPLATE = '/o/%s/g/%s/e/add-from-template';
  const PATH_EVENT_ADD_FROM_TEMPLATE = '/o/%s/g/%s/e/add/%d';
  const PATH_ORGANIZATION_PAGE = '/o/%s';
  const PATH_ORGANIZATION_PUBLISH_PAGE = '/o/%s/publish';
  const PATH_GROUP_PAGE = '/o/%s/g/%s';
  const PATH_GROUP_PUBLISH_PAGE = '/o/%s/g/%s/publish';
  const PATH_EVENT_PAGE = '/o/%s/g/%s/e/%d';
  const PATH_EVENT_EDIT_PAGE = '/o/%s/g/%s/e/%d/edit';
  const PATH_EVENT_PUBLISH_PAGE = '/o/%s/g/%s/e/%d/publish';
  const PATH_IMPORT_PAGE = '/o/%s/g/%s/imports/%d';
  const PATH_IMPORT_PUBLISH_PAGE = '/o/%s/g/%s/imports/%d/publish';
  const PATH_EXPORT_PAGE = '/o/%s/exports/%d';
  const PATH_EXPORT_PUBLISH_PAGE = '/o/%s/exports/%d/publish';
  const PATH_FILTER_PAGE = '/o/%s/filters/%d';
  const PATH_EVENT_TEMPLATE_PAGE = '/o/%s/event-templates/%d';
  const ADD_CSV_IMPORT_PATH = '/o/%s/g/%s/imports/add/csv';
  const ADD_CSV_EXPORT_PATH = '/o/%s/exports/add/csv';
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
    $this->drupalGet(sprintf(
      self::PATH_ORGANIZATION_PAGE,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    // User has access to event template selection form.
    $this->drupalGet(sprintf(
      self::PATH_SELECT_EVENT_TEMPLATE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    // User has access to event form with template selected.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_ADD_FROM_TEMPLATE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->eventtemplate->id()
    ));
    $this->assertResponse(200);
    $this->assertText(CreateEventTemplate::EVENT_DESCRIPTION);
    // User has access to group page.
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    // User has access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->assertResponse(200);

    // Unpublish event.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('One item unpublished.');

    // Verify that organizer cannot access event.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet(sprintf(
      self::PATH_ORGANIZATION_PAGE,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    // User has access to group page.
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    // User does not have access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->assertResponse(403);

    // Publish event and unpublish group.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('8 items unpublished.');

    // Verify that organizer cannot access group and event.
    $this->drupalLogin($this->organizer);
    // User has access to organization page.
    $this->drupalGet(sprintf(
      self::PATH_ORGANIZATION_PAGE,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    // User does not have access to group page.
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->assertResponse(403);

    // Publish event and group and unpublish organization.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('8 items published.');
    $this->drupalGet(sprintf(
      self::PATH_ORGANIZATION_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->drupalPostForm(NULL, [], t('Unpublish'));
    $this->assertText('13 items unpublished.');

    // Verify that organizer cannot access organization, group and event.
    $this->drupalLogin($this->organizer);
    // User does not have access to organization page.
    $this->drupalGet(sprintf(
      self::PATH_ORGANIZATION_PAGE,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(403);
    // User does not have access to group page.
    $this->drupalGet(sprintf(
      self::PATH_GROUP_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ));
    $this->assertResponse(403);
    // User does not have access to import page.
    $this->drupalGet(sprintf(
      self::PATH_IMPORT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->import->id()
    ));
    $this->assertResponse(403);
    // User does not have access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      2
    ));
    $this->assertResponse(403);
    // User does not have access to export page.
    $this->drupalGet(sprintf(
      self::PATH_EXPORT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      $this->export->id()
    ));
    $this->assertResponse(403);
    // User does not have access to filter page.
    $this->drupalGet(sprintf(
      self::PATH_FILTER_PAGE,
      PathHelper::transliterate($this->organization->label()),
      $this->filter->id()
    ));
    $this->assertResponse(403);
    // User does not have access to event template page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_TEMPLATE_PAGE,
      PathHelper::transliterate($this->organization->label()),
      $this->eventtemplate->id()
    ));
    $this->assertResponse(403);
    // User has access to event form with template selected but template is
    // not added.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_ADD_FROM_TEMPLATE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->eventtemplate->id()
    ));
    $this->assertResponse(200);
    $this->assertNoText(CreateEventTemplate::EVENT_TITLE);
    $this->assertNoText(CreateEventTemplate::EVENT_DESCRIPTION);

    // Publish import and events.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::PATH_IMPORT_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->import->id()
    ));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('6 items published.');

    // Verify that organizer can access import and events.
    $this->drupalLogin($this->organizer);
    // User has access to import page.
    $this->drupalGet(sprintf(
      self::PATH_IMPORT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->import->id()
    ));
    $this->assertResponse(200);
    // User has access to event page.
    $this->drupalGet(sprintf(
      self::PATH_EVENT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      2
    ));
    $this->assertResponse(200);

    // Publish export.
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::PATH_EXPORT_PUBLISH_PAGE,
      PathHelper::transliterate($this->organization->label()),
      $this->export->id()
    ));
    $this->drupalPostForm(NULL, [], t('Publish'));
    $this->assertText('One item published.');

    // Verify that manager can access export.
    $this->drupalGet(sprintf(
      self::PATH_EXPORT_PAGE,
      PathHelper::transliterate($this->organization->label()),
      $this->export->id()
    ));
    $this->assertResponse(200);
  }

}
