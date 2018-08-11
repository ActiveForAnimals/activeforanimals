<?php

namespace Drupal\afa_reroute_drupal_core_paths\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Reroute for selected paths.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $user_login_route = $collection->get('user.login');
    if ($user_login_route !== NULL) {
      $user_login_route->setPath('/log-in');
    }
    $user_register_route = $collection->get('user.register');
    if ($user_register_route !== NULL) {
      $user_register_route->setPath('/sign-up');
    }
    $user_register_route = $collection->get('user.pass');
    if ($user_register_route !== NULL) {
      $user_register_route->setPath('/user/reset-password');
    }
  }

}
