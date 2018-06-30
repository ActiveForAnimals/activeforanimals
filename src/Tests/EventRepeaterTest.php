<?php

namespace Drupal\activeforanimals\Tests;

use DateInterval;
use DateTime;
use Drupal\activeforanimals\Tests\Helper\CreateEvent;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for repeating events.
 *
 * @group activeforanimals
 */
class EventRepeaterTest extends WebTestBase {

  const STEP = 3;
  const FREQUENCY = 'D';
  const REPEATS = 4;
  const EDIT_EVENT_PATH = '/o/%s/g/%s/e/%d/repeat';

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
   * @var \Drupal\effective_activism\Entity\Organization
   */
  private $organization;

  /**
   * The group to host the event.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group;

  /**
   * The event to test with results.
   *
   * @var \Drupal\effective_activism\Entity\Event
   */
  private $event;

  /**
   * Container for the manager user.
   *
   * @var User
   */
  private $manager;

  /**
   * Container for the organizer user.
   *
   * @var User
   */
  private $organizer;

  /**
   * Start date.
   *
   * @var \DateTime
   */
  private $startDate;

  /**
   * End date.
   *
   * @var \DateTime
   */
  private $endDate;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->group = Group::load(1);
    $this->event = (new CreateEvent($this->group, $this->organizer))->execute();
    $this->startDate = new DateTime($this->event->start_date->value);
    $this->endDate = new DateTime($this->event->end_date->value);
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->organizer);
    $this->assertEqual(1, count(GroupHelper::getEvents($this->group, 0, 0, FALSE)), 'One event available');
    $this->drupalPostForm(sprintf(
      self::EDIT_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      $this->event->id()
    ), [
      'step' => self::STEP,
      'frequency' => self::FREQUENCY,
      'repeats' => self::REPEATS,
    ], t('Repeat'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Repeated event %d times.', self::REPEATS), 'Repeated event.');
    $events = GroupHelper::getEvents($this->group, 0, 0, TRUE);
    $this->assertEqual(self::REPEATS + 1, count($events), sprintf('%d events available', self::REPEATS + 1));
    foreach ($events as $event) {
      $this->assertEqual($event->start_date->value, $this->startDate->format('Y-m-d\TH:i:s'), 'Event start date matches step and frequency');
      $this->assertEqual($event->end_date->value, $this->endDate->format('Y-m-d\TH:i:s'), 'Event end date matches step and frequency');
      $this->startDate->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY
      )));
      $this->endDate->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY
      )));
    }
  }

}
