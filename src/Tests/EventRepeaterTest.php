<?php

namespace Drupal\activeforanimals\Tests;

use DateInterval;
use DateTime;
use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\EventRepeater;
use Drupal\effective_activism\Entity\Group;
use Drupal\effective_activism\Helper\DateHelper;
use Drupal\effective_activism\Helper\GroupHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Drupal\simpletest\WebTestBase;

/**
 * Function test for creating repeated events.
 *
 * @group activeforanimals
 */
class EventRepeaterTest extends WebTestBase {

  const CREATE_EVENT_PATH = '/o/%s/g/%s/e/add';
  const EDIT_EVENT_PATH = '/o/%s/g/%s/e/%d/edit';
  const EVENT_ID = '1';
  const EVENT_ID_2 = '4';
  const STEP = 3;
  const FREQUENCY_DAY = 'D';
  const FREQUENCY_WEEK = 'W';
  const LOCATION_ADDRESS = 'Copenhagen, Denmark';
  const LOCATION_EXTRA_INFORMATION = 'Test location';

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
   * The group to host the repeated events.
   *
   * @var \Drupal\effective_activism\Entity\Group
   */
  private $group;

  /**
   * Manager.
   *
   * @var User
   */
  private $manager;

  /**
   * Organizer.
   *
   * @var User
   */
  private $organizer;

  /**
   * Start date.
   *
   * @var \DateTime
   */
  private $start_date;

  /**
   * End date.
   *
   * @var \DateTime
   */
  private $end_date;

  /**
   * End on date.
   *
   * @var \DateTime
   */
  private $end_on_date;

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
    $this->group = Group::load('1');
    $this->start_date = new DateTime('today +1 day');
    $this->end_date = new DateTime('today +1 day +1 hour');
    $this->end_on_date = new DateTime('today +3 days');
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->organizer);
    $this->drupalGet(sprintf(
      self::CREATE_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label())
    ));
    // Test repeat feature.
    $this->drupalPostForm(NULL, [
      'start_date[0][value]' => $this->start_date->format('Y-m-d H:i'),
      'end_date[0][value]' => $this->end_date->format('Y-m-d H:i'),
      'location[0][address]' => self::LOCATION_ADDRESS,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
      $this->getElementName('//input[contains(@name, "[step]")]') => self::STEP,
      $this->getElementName('//select[contains(@name, "[frequency]")]') => self::FREQUENCY_DAY,
      $this->getElementName('//input[contains(@name, "[end_on_date]")]') => '',
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText('Created event.', 'Added a new event entity.');
    $events = GroupHelper::getEvents($this->group);
    $this->assertTrue(count($events) === EventRepeater::MAX_REPEATS, 'Correct number of repeated events');
    foreach ($events as $event) {
      $this->assertEqual($event->start_date->value, $this->start_date->format('Y-m-d\TH:i:s'), 'Event start date matches step and frequency');
      $this->assertEqual($event->end_date->value, $this->end_date->format('Y-m-d\TH:i:s'), 'Event end date matches step and frequency');
      $this->start_date->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY_DAY
      )));
      $this->end_date->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY_DAY
      )));
    }
    // Test rescheduling by changing date of later event.
    $this->start_date = new DateTime('today +1 year +1 day');
    $this->end_date = new DateTime('today +1 year +1 day +1 hour');
    $this->drupalGet(sprintf(self::EDIT_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      self::EVENT_ID_2
    ));
    $this->drupalPostForm(NULL, [
      'start_date[0][value]' => $this->start_date->format('Y-m-d H:i'),
      'end_date[0][value]' => $this->end_date->format('Y-m-d H:i'),
    ], t('Save'));
    $this->assertResponse(200);
    $events = GroupHelper::getEvents($this->group);
    $this->assertTrue(count($events) === EventRepeater::MAX_REPEATS, 'Correct number of repeated events');
    foreach ($events as $event) {
      $this->assertEqual($event->start_date->value, $this->start_date->format('Y-m-d\TH:i:s'), 'Event start date matches step and frequency: ' . $event->id());
      $this->assertEqual($event->end_date->value, $this->end_date->format('Y-m-d\TH:i:s'), 'Event end date matches step and frequency');
      $this->start_date->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY_DAY
      )));
      $this->end_date->add(new DateInterval(sprintf('P%d%s',
        self::STEP,
        self::FREQUENCY_DAY
      )));
    }
    // Test rescheduling by changing event repeater step/frequency.
    $this->drupalGet(sprintf(self::EDIT_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      self::EVENT_ID_2
    ));
    $this->drupalPostForm(NULL, [
      $this->getElementName('//select[contains(@name, "[frequency]")]') => self::FREQUENCY_WEEK,
      $this->getElementName('//input[contains(@name, "[end_on_date]")]') => '',
    ], t('Save'));
    // Test end on date.
    $this->drupalGet(sprintf(self::EDIT_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      self::EVENT_ID
    ));
    $this->drupalPostForm(NULL, [
      $this->getElementName('//input[contains(@name, "[end_on_date]")]') => $this->end_on_date->format('Y-m-d H:i'),
    ], t('Save'));
    $this->assertResponse(200);
    $events = GroupHelper::getEvents($this->group);
    foreach ($events as $event) {
      $event_start_date = new DateTime($event->start_date->value);
      $this->assertTRUE($event_start_date->format('U') <= $this->end_on_date->format('U'), 'Event start date is older than end on date.');
    }
    // Test disabling event repeater
    $this->drupalGet(sprintf(self::EDIT_EVENT_PATH,
      PathHelper::transliterate($this->organization->label()),
      PathHelper::transliterate($this->group->label()),
      self::EVENT_ID
    ));
    $this->drupalPostForm(NULL, [
      $this->getElementName('//input[contains(@name, "[step]")]') => 0,
      $this->getElementName('//input[contains(@name, "[end_on_date]")]') => '',
    ], t('Save'));
    $this->assertResponse(200);
    $events = GroupHelper::getEvents($this->group);
    $this->assertTrue(count($events) === 1, 'Correct number of repeated events after disabling repeater');
    $remaining_event = array_pop($events);
    $this->assertTrue($remaining_event->id() === self::EVENT_ID, 'Correct event remains');
  }

  /**
   * Gets HTML element name.
   *
   * @param string $xpath
   *   Xpath of the element.
   *
   * @return string
   *   The name of the element.
   */
  private function getElementName($xpath) {
    $retval = '';
    /** @var \SimpleXMLElement[] $elements */
    if ($elements = $this->xpath($xpath)) {
      foreach ($elements[0]->attributes() as $name => $value) {
        if ($name === 'name') {
          $retval = $value;
          break;
        }
      }
    }
    return (string) $retval;
  }

}
