<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\Component\Utility\Random;
use Drupal\simpletest\WebTestBase;

/**
 * User registration test.
 *
 * @group activeforanimals
 */
class RegisterUserTest extends WebTestBase {

  const PATH_REGISTER_USER = 'user/register';
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
  ];

  /**
   * Run test.
   */
  public function testDo() {
    $this->drupalGet(self::PATH_REGISTER_USER);
    $random_generator = new Random();
    $name = $random_generator->word(8);
    $mail = self::USER_MAIL;
    $password = $random_generator->word(8);
    $reply = self::CHALLENGE_RESPONSE;
    $wrong_reply = '';
    $hidden_field = $random_generator->word(8);
    // Fail scenario 1.
    $this->drupalPostForm(NULL, [
      'name' => $name,
      'mail' => $mail,
      'afa_challenge' => $reply,
      'pass[pass1]' => $password,
      'pass[pass2]' => $password,
      'afa_username' => $hidden_field,
    ], t('Create new account'));
    $this->assertText('There was a problem with your form submission. Please refresh the page and try again.', 'Ran fail scenario 1');
    // Fail scenario 2.
    $this->drupalPostForm(NULL, [
      'name' => $name,
      'mail' => $mail,
      'afa_challenge' => $wrong_reply,
      'pass[pass1]' => $password,
      'pass[pass2]' => $password,
      'afa_username' => '',
    ], t('Create new account'));
    $this->assertText('There was a problem with your form submission. Please refresh the page and try again.', 'Ran fail scenario 2');
    // Test successful registration.
    $this->drupalPostForm(NULL, [
      'name' => $name,
      'mail' => $mail,
      'afa_challenge' => $reply,
      'pass[pass1]' => $password,
      'pass[pass2]' => $password,
      'afa_username' => '',
    ], t('Create new account'));
    $this->assertText('Registration successful. You are now logged in.', 'Successfully created user');

  }

}
