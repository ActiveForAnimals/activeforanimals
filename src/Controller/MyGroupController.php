<?php

namespace Drupal\activeforanimals\Controller;

use Drupal;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\effective_activism\AccessControlHandler\AccessControl;
use Drupal\effective_activism\Helper\AccountHelper;
use Drupal\effective_activism\Helper\PathHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Renders a list of events created by current user.
 *
 * @ingroup effective_activism
 */
class MyGroupController extends ControllerBase {

  const CACHE_MAX_AGE = Cache::PERMANENT;

  const CACHE_TAGS = [
    'user',
  ];

  /**
   * Returns a RedirectResponse.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   A request.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A response.
   */
  public function gotoGroup(Request $request) {
    $response = $this->redirect('<front>');
    $user = Drupal::currentUser();
    $organized_groups = AccountHelper::getGroups($user);
    if (count($organized_groups) === 1 && AccessControl::isGroupStaff($organized_groups)->isAllowed()) {
      $group = array_pop($organized_groups);
      $response = $this->redirect('entity.group.canonical', [
        'organization' => PathHelper::transliterate($group->organization->entity->label()),
        'group' => PathHelper::transliterate($group->label()),
      ]);
    }
    return $response;
  }

}
