<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Event;
use Drupal\effective_activism\Entity\EventTemplate;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating event templates.
 *
 * @group activeforanimals
 */
class EventTemplateTest extends WebTestBase {

  const ADD_EVENT_TEMPLATE_PATH = '/o/%s/event-templates/add';
  const DELETE_EVENT_TEMPLATE_PATH = '/o/%s/event-templates/%d/delete';
  const SELECT_EVENT_TEMPLATE_PATH = '/o/%s/g/%s/e/add-from-template';
  const TITLE = 'Test event template';
  const EVENT_TITLE = 'A sample event title';
  const EVENT_DESCRIPTION = 'A sample event description';
  const EVENT_START_DATE = '2018-12-31 11:00';
  const EVENT_END_DATE = '2018-12-31 12:00';
  const EVENT_START_DATE_FORMATTED = '12/31/2018 - 11:00';
  const EVENT_END_DATE_FORMATTED = '12/31/2018 - 12:00';
  const EVENT_LOCATION_ADDRESS = '';
  const EVENT_LOCATION_EXTRA_INFORMATION = 'A sample location';

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
   * The organization to host the event template.
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
    // Disable user time zones.
    // This is required in order for events to register correct time.
    $systemDate = Drupal::configFactory()->getEditable('system.date');
    $systemDate->set('timezone.default', 'UTC');
    $systemDate->save(TRUE);
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
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::ADD_EVENT_TEMPLATE_PATH,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'name[0][value]' => self::TITLE,
      'event_title[0][value]' => self::EVENT_TITLE,
      'event_description[0][value]' => self::EVENT_DESCRIPTION,
      'event_start_date[0][value]' => self::EVENT_START_DATE,
      'event_end_date[0][value]' => self::EVENT_END_DATE,
      'event_location[0][address]' => self::EVENT_LOCATION_ADDRESS,
      'event_location[0][extra_information]' => self::EVENT_LOCATION_EXTRA_INFORMATION,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s event template.', self::TITLE), 'Created a new event template.');
    $this->drupalGet(sprintf(
      self::SELECT_EVENT_TEMPLATE_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'event_template' => 1,
    ], t('Select'));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [], t('Save'));
    $this->assertText(self::EVENT_TITLE);
    $this->assertText(self::EVENT_DESCRIPTION);
    $this->assertText(self::EVENT_START_DATE_FORMATTED);
    $this->assertText(self::EVENT_END_DATE_FORMATTED);
    $this->assertText(self::EVENT_LOCATION_EXTRA_INFORMATION);
    // Verify that event template is added to event.
    $event_template = EventTemplate::load('1');
    $event = Event::load('1');
    $this->assertTrue($event->event_template->target_id === $event_template->id(), 'Event template added to event');
    // Verify that event template cannot be deleted.
    $this->drupalGet(sprintf(
      self::DELETE_EVENT_TEMPLATE_PATH,
      PathHelper::transliterate($this->organization->label()),
      1
    ));
    $this->drupalPostForm(NULL, [], t('Delete'));
    $this->assertResponse(200);
    $this->assertText(t('This template is in use and cannot be deleted.'));
  }

}
