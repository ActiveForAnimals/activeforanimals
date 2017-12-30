<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating groups.
 *
 * @group activeforanimals
 */
class GroupTest extends WebTestBase {

  const ADD_GROUP_PATH = '/create-group';
  const TITLE = 'Test group';
  const DESCRIPTION = 'Test group description';
  const WEBSITE = 'http://example.com';
  const PHONE_NUMBER = '+45 12345678';
  const EMAIL_ADDRESS = 'test@example.com';
  const LOCATION_ADDRESS = '';
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
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->organization = (new CreateOrganization($this->manager, $this->organizer))->execute();
    $this->timezone = 'inherit';
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(self::ADD_GROUP_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::TITLE,
      'description[0][value]' => self::DESCRIPTION,
      'website[0][value]' => self::WEBSITE,
      'phone_number[0][value]' => self::PHONE_NUMBER,
      'email_address[0][value]' => self::EMAIL_ADDRESS,
      'location[0][address]' => self::LOCATION_ADDRESS,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
      'organization[0][target_id]' => $this->organization->id(),
      'timezone' => $this->timezone,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s group.', self::TITLE), 'Creating a new group.');
    $this->assertText(self::TITLE, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION, 'Set description correctly.');
    $this->assertText(self::WEBSITE, 'Set website correctly.');
    $this->assertText(self::PHONE_NUMBER, 'Set phone number correctly.');
    $this->assertText(self::EMAIL_ADDRESS, 'Set e-mail address correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION, 'Set location extra information correctly.');
  }

}
