<?php

namespace Drupal\afa_reroute_drupal_core_paths\Tests;

use Drupal\Component\Utility\Random;
use Drupal\simpletest\WebTestBase;

/**
 * Rerouted paths test.
 *
 * @group activeforanimals
 */
class ReroutedPathsTest extends WebTestBase {

  const PATH_REGISTER_USER = 'sign-up';
  const PATH_LOGIN_USER = 'log-in';
  const USER_MAIL = 'no-reply@activeforanimals.com';
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
    'afa_reroute_drupal_core_paths',
  ];

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalGet(self::PATH_REGISTER_USER);
    $this->assertResponse(200);
    $random_generator = new Random();
    $name = $random_generator->word(8);
    $password = $random_generator->word(8);
    // Test successful registration.
    $this->drupalPostForm(NULL, [
      'name' => $name,
      'mail' => self::USER_MAIL,
      'afa_challenge' => self::CHALLENGE_RESPONSE,
      'pass[pass1]' => $password,
      'pass[pass2]' => $password,
      'afa_username' => '',
    ], t('Create new account'));
    $this->assertResponse(200);
    $this->assertText('Log out', 'Successfully created user');
    $this->drupalLogout();
    // Test successful login.
    $this->drupalGet(self::PATH_LOGIN_USER);
    $this->assertResponse(200);
    $this->drupalPostForm(NULL, [
      'name' => $name,
      'pass' => $password,
    ], t('Log in'));
    $this->assertResponse(200);
    $this->assertText('Log out', 'Successfully logged in user');
  }

}
