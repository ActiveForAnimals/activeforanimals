<?php

namespace Drupal\afa_facebook_event_listener\Controller;

use Drupal;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for the Facebook webhook.
 */
class WebHookController extends ControllerBase {

  /**
   * Returns a render array.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Drupal\Core\Render\HtmlResponse
   *   A render array.
   */
  public function content(Request $request) {
    $verifyToken = Drupal::config('effective_activism.settings')->get('facebook_verify_token');
    $appSecret = Drupal::config('effective_activism.settings')->get('facebook_app_secret');
    $hubChallenge = (int) $request->query->get('hub_challenge');
    $hubVerityToken = $request->get('hub_verify_token');
    $response = new HtmlResponse();
    if (!isset($verifyToken) || !isset($appSecret)) {
      $response
        ->setContent('missing setting vars')
        ->addCacheableDependency((new CacheableMetadata())->setCacheMaxAge(0));
      return $response;
    }
    elseif ($hubVerityToken === $verifyToken) {
      $response
        ->setContent($hubChallenge)
        ->addCacheableDependency((new CacheableMetadata())->setCacheMaxAge(0));
      Drupal::logger('afa_facebook_event_listener')->debug('Verify matched');
      Drupal::logger('afa_facebook_event_listener')->debug(json_encode($request->query->all()));
      return $response;
    }
    elseif (!empty($request->headers->get('X-Hub-Signature'))) {
      $signature = $request->headers->get('X-Hub-Signature');
      if ($signature === sha1($request->getContent() . $appSecret)) {
        // Signature match, look up user facebook id and afa group and add accordingly.
        Drupal::logger('afa_facebook_event_listener')->debug('Signature matched');
        Drupal::logger('afa_facebook_event_listener')->debug($request->getContent());
      }
      else {
        Drupal::logger('afa_facebook_event_listener')->debug('Signature did not match');
        Drupal::logger('afa_facebook_event_listener')->debug($request->getContent());
      }
    }
    else {
      $response->addCacheableDependency((new CacheableMetadata())->setCacheMaxAge(0));
      Drupal::logger('afa_facebook_event_listener')->notice('Verify did not match and/or signature was not found');
      Drupal::logger('afa_facebook_event_listener')->notice(json_encode($request->request->all()));
      return $response;
    }
  }

}
