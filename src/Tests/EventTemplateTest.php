<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating event templates.
 *
 * @group activeforanimals
 */
class EventTemplateTest extends WebTestBase {

  const ADD_EVENT_TEMPLATE_PATH = '/create-event-template';
  const SELECT_EVENT_TEMPLATE_PATH = '/select-event-template';
  const TITLE = 'Test event template';
  const EVENT_TITLE = 'A sample event title';
  const EVENT_DESCRIPTION = 'A sample event description';

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
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(self::ADD_EVENT_TEMPLATE_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'name[0][value]' => self::TITLE,
      'organization[0][target_id]' => $this->organization->id(),
      'event_title[0][value]' => self::EVENT_TITLE,
      'event_description[0][value]' => self::EVENT_DESCRIPTION,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s event template.', self::TITLE), 'Created a new event template.');
    $this->drupalGet(self::SELECT_EVENT_TEMPLATE_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'organization' => $this->organization->id(),
      'event_template' => 1,
    ], t('Select'));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [], t('Save'));
    $this->assertText(self::EVENT_TITLE);
    $this->assertText(self::EVENT_DESCRIPTION);
  }

}
