<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Entity\Group;
use Drupal\simpletest\WebTestBase;

/**
 * Function tests for creating groups.
 *
 * @group activeforanimals
 */
class GroupTest extends WebTestBase {

  const ADD_GROUP_PATH = '/create-group';
  const TITLE_1 = 'Test group 1';
  const DESCRIPTION_1 = 'Test group description 1';
  const WEBSITE_1 = 'https://example.com/1';
  const PHONE_NUMBER_1 = '+45 12345678';
  const EMAIL_ADDRESS_1 = 'test1@example.com';
  const LOCATION_ADDRESS_1 = '';
  const LOCATION_EXTRA_INFORMATION_1 = 'Test location 1';
  const TITLE_2 = 'Test group 2';
  const DESCRIPTION_2 = 'Test group description 2';
  const WEBSITE_2 = 'https://example.com/2';
  const PHONE_NUMBER_2 = '+45 87654321';
  const EMAIL_ADDRESS_2 = 'test2@example.com';
  const LOCATION_ADDRESS_2 = '';
  const LOCATION_EXTRA_INFORMATION_2 = 'Test location 2';

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
      'title[0][value]' => self::TITLE_1,
      'description[0][value]' => self::DESCRIPTION_1,
      'website[0][value]' => self::WEBSITE_1,
      'phone_number[0][value]' => self::PHONE_NUMBER_1,
      'email_address[0][value]' => self::EMAIL_ADDRESS_1,
      'location[0][address]' => self::LOCATION_ADDRESS_1,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION_1,
      'organization[0][target_id]' => $this->organization->id(),
      'timezone' => $this->timezone,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Created the %s group.', self::TITLE_1), 'Creating a new group.');
    $this->assertText(self::TITLE_1, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION_1, 'Set description correctly.');
    $this->assertText(self::WEBSITE_1, 'Set website correctly.');
    $this->assertText(self::PHONE_NUMBER_1, 'Set phone number correctly.');
    $this->assertText(self::EMAIL_ADDRESS_1, 'Set e-mail address correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION_1, 'Set location extra information correctly.');

    // Verity that organizer can edit existing group.
    $this->drupalLogin($this->organizer);
    $this->drupalGet(sprintf('%s/edit', Group::load('1')->toUrl()->toString()));
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'title[0][value]' => self::TITLE_2,
      'description[0][value]' => self::DESCRIPTION_2,
      'website[0][value]' => self::WEBSITE_2,
      'phone_number[0][value]' => self::PHONE_NUMBER_2,
      'email_address[0][value]' => self::EMAIL_ADDRESS_2,
      'location[0][address]' => self::LOCATION_ADDRESS_2,
      'location[0][extra_information]' => self::LOCATION_EXTRA_INFORMATION_2,
    ], t('Save'));
    $this->assertResponse(200);
    $this->assertText(sprintf('Saved the %s group.', self::TITLE_2), 'Updated a group as organizer.');
    $this->assertText(self::TITLE_2, 'Set title correctly.');
    $this->assertText(self::DESCRIPTION_2, 'Set description correctly.');
    $this->assertText(self::WEBSITE_2, 'Set website correctly.');
    $this->assertText(self::PHONE_NUMBER_2, 'Set phone number correctly.');
    $this->assertText(self::EMAIL_ADDRESS_2, 'Set e-mail address correctly.');
    $this->assertText(self::LOCATION_EXTRA_INFORMATION_2, 'Set location extra information correctly.');
  }

}
