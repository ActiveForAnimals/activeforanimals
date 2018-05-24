<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
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
	const TITLE = 'Test event';
	const DESCRIPTION = 'Test event description';
	const STARTDATE = '2016-01-01 11:00';
	const STARTDATEFORMATTED = '2016-01-01 11:00';
	const ENDDATE = '2016-01-01 12:00';
	const ENDDATEFORMATTED = '2016-01-01 12:00';
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
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->manager = $this->drupalCreateUser();
		$this->organizer = $this->drupalCreateUser();
		$this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
		$this->group = Group::load('1');
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
		$this->drupalPostForm(NULL, [
			'start_date[0][value]' => self::STARTDATE,
			'end_date[0][value]' => self::ENDDATE,
			'location[0][address]' => self::LOCATION_ADDRESS,
			'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
			'event_repeater[0][inline_entity_form][step][0][value]' => '3',
			'event_repeater[0][inline_entity_form][frequency]' => 'day',
		], t('Save'));
		$this->assertResponse(200);
		$events = GroupHelper::getEvents($this->group);
		$this->assertTrue(count($events) === 5, 'Correct number of repeated events');
	}

}