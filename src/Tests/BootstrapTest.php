<?php

namespace Drupal\activeforanimals\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Function tests for bootstrapping installation profile.
 *
 * @group activeforanimals
 */
class BootstrapTest extends WebTestBase {

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
    $this->drupalGet('<front>');
    $this->assertResponse(200);
  }

}
