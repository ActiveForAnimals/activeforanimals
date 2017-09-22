<?php

namespace Drupal\activeforanimals\Tests;

use Drupal;
use Drupal\activeforanimals\Tests\Helper\CreateOrganization;
use Drupal\effective_activism\Constant as EffectiveActivismConstant;
use Drupal\effective_activism\Helper\OrganizationHelper;
use Drupal\effective_activism\Entity\Organization;
use Drupal\effective_activism\Helper\InvitationHelper;
use Drupal\simpletest\WebTestBase;

/**
 * User registration test.
 *
 * @group activeforanimals
 */
class InvitationTest extends WebTestBase {

  const PATH_REGISTER_USER = 'user/register';
  const TEST_NAME = 'Test user';
  const TEST_PASSWORD = 'GoVegan';
  const TEST_EMAIL_ADDRESS = 'no-reply@activeforanimals.com';
  const TEST_SUBJECT = '[AFA] Invitation to join %s';
  const TEST_BODY = 'Hello  You have been invited to join %s as manager on http://www.activeforanimals.com.  To join, follow these steps:  - Create an account at http://www.activeforanimals.com/user using the e-mail address that this e-mail was sent to.  - When logged in, confirm that you want to join %s.';
  const CHALLENGE_RESPONSE = 'fruit';

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
   * The test organization.
   *
   * @var Organization
   */
  private $organization;

  /**
   * The test group.
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
    // Create users and structure.
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
    // Setup test mail interface.
    $this->config('system.mail')->set('interface.default', 'test_mail_collector')->save();
    Drupal::state()->set('system.test_mail_collector', []);
    $language_interface = Drupal::languageManager()->getCurrentLanguage();
    // Verify that only one user is manager.
    $this->assertEqual(1, count($this->organization->get('managers')->getValue()), 'Correct number of managers');
    // Invite new manager.
    $this->drupalLogin($this->manager);
    $organization_edit_path = sprintf('%s/edit', $this->organization->toUrl()->toString());
    $this->drupalGet($organization_edit_path);
    $this->assertResponse(200);
    $this->assertFieldByXPath('//input[@type="submit" and @value="Invite new manager"]', NULL, 'Button to invite new managers exists');
    $this->drupalPostAjaxForm(NULL, [
      'logo[0][fids]' => '',
    ], $this->getElementName('//input[@type="submit" and @value="Invite new manager"]'));
    $this->assertResponse(200);
    $post_data = [
      $this->getElementName('//input[@name="managers[form][invite_email_address]"]') => self::TEST_EMAIL_ADDRESS,
    ];
    $this->assertFieldByXPath('//input[@type="submit" and @value="Invite manager"]', NULL, 'Button to invite managers exists');
    $this->drupalPostAjaxForm(NULL, $post_data, $this->getElementName('//input[@type="submit" and @value="Invite manager"]'));
    $this->assertResponse(200);
    $this->assertText(sprintf('An invitation to join your organization as manager will be sent to the user with the e-mail address %s.', self::TEST_EMAIL_ADDRESS), 'Invitation sent');
    // Send a test message that simpletest_mail_alter should cancel.
    Drupal::service('plugin.manager.mail')->mail('simpletest', EffectiveActivismConstant::MAIL_KEY_INVITATION_MANAGER, self::TEST_EMAIL_ADDRESS, $language_interface->getId());
    $captured_emails = Drupal::state()->get('system.test_mail_collector');
    $this->assertEqual($captured_emails[0]['id'], 'effective_activism_effective_activism_invitation_manager', 'mail key matches');
    $this->assertEqual($captured_emails[0]['to'], self::TEST_EMAIL_ADDRESS, 'e-mail recipient matches');
    $this->assertEqual($captured_emails[0]['subject'], sprintf(self::TEST_SUBJECT, $this->organization->get('title')->value), 'subject matches');
    $this->assertEqual(preg_replace('/\s+/', '', $captured_emails[0]['body']), preg_replace('/\s+/', '', sprintf(self::TEST_BODY, $this->organization->get('title')->value, $this->organization->get('title')->value)));
    // Create user with e-mail address and log in.
    $this->drupalLogout();
    $this->drupalGet(self::PATH_REGISTER_USER);
    $this->drupalPostForm(NULL, [
      'name' => self::TEST_NAME,
      'mail' => self::TEST_EMAIL_ADDRESS,
      'afa_challenge' => self::CHALLENGE_RESPONSE,
      'pass[pass1]' => self::TEST_PASSWORD,
      'pass[pass2]' => self::TEST_PASSWORD,
      'afa_username' => '',
    ], t('Create new account'));
    $this->assertText('Registration successful. You are now logged in.', 'Successfully created user');
    // Accept invitation.
    $this->assertText(sprintf('You have been invited to join %s as %s. Please accept or decline the invitation.', $this->organization->get('title')->value, 'manager'), 'Detected invitation form');
    $this->drupalPostForm(NULL, [], t('Accept'));
    $this->assertResponse(200);
    $this->assertText(sprintf('You are now %s for %s.', 'manager', $this->organization->get('title')->value), 'Successfully accepted invitation');
    // Verify that user is manager.
    $this->drupalGet($organization_edit_path);
    $this->assertText(self::TEST_NAME, 'User is manager of organization');
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
