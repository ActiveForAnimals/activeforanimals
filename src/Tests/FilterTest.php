<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateEventTemplate;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating filters.
 *
 * @group activeforanimals
 */
class FilterTest extends WebTestBase {

  const ADD_FILTER_PATH = '/o/%s/filters/add';
  const EVENT_TEMPLATE_TITLE = 'Test template';
  const TITLE = 'Test filter';

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
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group;

  /**
   * The first event to test with filters.
   *
   * @var \Drupal\effective_activism\Entity\Event
   */
  private $event1;

  /**
   * The second event to test with filters.
   *
   * @var \Drupal\effective_activism\Entity\Event
   */
  private $event2;

  /**
   * The event template to test with filters.
   *
   * @var \Drupal\effective_activism\Entity\EventTemplate
   */
  private $eventTemplate;

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
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = Group::load(1);
    $this->eventTemplate = (new CreateEventTemplate($this->organization, $this->manager, self::EVENT_TEMPLATE_TITLE))->execute();
    $this->event1 = (new CreateEvent($this->group, $this->organizer))->execute();
    $this->event2 = (new CreateEvent($this->group, $this->organizer, NULL, [
      'event_template' => $this->eventTemplate->id(),
    ]))->execute();
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(sprintf(
      self::ADD_FILTER_PATH,
      PathHelper::transliterate($this->organization->label())
    ));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'name[0][value]' => self::TITLE,
      sprintf('event_templates[%d]', $this->eventTemplate->id()) => $this->eventTemplate->id(),
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(self::EVENT_TEMPLATE_TITLE, 'Template found.');
    $this->assertText('One event', 'One event included.');
    $this->assertText(sprintf('Created the %s filter.', self::TITLE), 'Created a new filter.');
  }

}
