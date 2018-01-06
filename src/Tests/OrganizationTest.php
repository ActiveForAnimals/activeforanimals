<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating organizations.
 *
 * @group activeforanimals
 */
class OrganizationTest extends WebTestBase {

  const ADD_ORGANIZATION_PATH = 'create-organization';
  const TITLE = 'Test organization';
  const DESCRIPTION = 'Test organization description';
  const WEBSITE = 'https://example.com';
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
   * The test manager.
   *
   * @var User
   */
  private $manager;

  /**
   * Organization timezone.
   *
   * @var string
   */
  private $timezone;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->manager = $this->drupalCreateUser();
    $this->organizer = $this->drupalCreateUser();
    $this->timezone = \Drupal::config('system.date')->get('timezone.default');
  }

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalLogin($this->manager);
    $this->drupalGet(self::ADD_ORGANIZATION_PATH);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::TITLE,
      'description[0][value]' => self::DESCRIPTION,
      'website[0][value]' => self::WEBSITE,
      'phone_number[0][value]' => self::PHONE_NUMBER,
      'email_address[0][value]' => self::EMAIL_ADDRESS,
      'location[0][address]' => self::LOCATION_ADDRESS,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION,
      'timezone' => $this->timezone,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s organization.', self::TITLE), 'Creating a new organization.');
    $this->assertText(self::TITLE, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION, 'Set description correctly.');
    $this->assertText(self::WEBSITE, 'Set website correctly.');
    $this->assertText(self::PHONE_NUMBER, 'Set phone number correctly.');
    $this->assertText(self::EMAIL_ADDRESS, 'Set e-mail address correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION, 'Set location extra information correctly.');
  }

}
