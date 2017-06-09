<?php

namespace Drupal\activeforanimals\Helper;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Autogenerated paths and routes for static pages.
 */
class StaticPageRoutes {

  const ROUTEBASENAME = 'activeforanimals';

  const CONTROLLER = 'Drupal\activeforanimals\Controller\StaticPageController::content';

  const PERMISSIONS = [
    '_permission'  => 'access content',
  ];

  /**
   * A collection of routes.
   *
   * @var \Symfony\Component\Routing\RouteCollection
   */
  private $collection;

  /**
   * The root path of the static page location.
   *
   * @var string
   */
  private $rootpath;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->collection = new RouteCollection();
    $this->rootpath = sprintf('%s/docs', drupal_get_path('profile', 'activeforanimals'));
    $this->traversePath($this->rootpath);
  }

  /**
   * Returns static page routes.
   *
   * @return \Symfony\Component\Routing\RouteCollection
   *   A collection of routes.
   */
  public function routes() {
    return $this->collection;
  }

  /**
   * Builds routes from a path.
   *
   * @param string $path
   *   A path to iterate over.
   */
  private function traversePath($path) {
    $handle = opendir($path);
    if ($handle) {
      while (($element = readdir($handle)) !== FALSE) {
        if (
          $element === '.' ||
          $element === '..'
        ) {
          continue;
        }
        $element_path = sprintf('%s/%s', $path, $element);
        if (is_dir($element_path)) {
          $this->traversePath($element_path);
        }
        else {
          $path_parts = pathinfo($element_path);
          // If this a markdown file, add a route to it.
          if ($path_parts['extension'] === 'md') {
            $route_name = NULL;
            $route_path = NULL;
            $title = NULL;
            $path_without_root = substr($path, strlen($this->rootpath) + 1);
            $route_name_subsection = $this->machinify(implode('.', explode(DIRECTORY_SEPARATOR, $path_without_root)));
            $route_name_file = $this->machinify($path_parts['filename']);
            // Treat README.md files as the directory index file.
            if ($route_name_file === 'readme') {
              $route_name = sprintf('%s.%s', self::ROUTEBASENAME, $route_name_subsection);
              $route_path = str_replace('.', '/', $route_name_subsection);
              $path_components = explode(DIRECTORY_SEPARATOR, $path_parts['dirname']);
              $title = str_replace('-', ' ', end($path_components));
            }
            else {
              $route_name = sprintf('%s.%s.%s', self::ROUTEBASENAME, $route_name_subsection, $route_name_file);
              $route_path = sprintf('%s/%s', str_replace('.', '/', $route_name_subsection), $route_name_file);
              $title = str_replace('-', ' ', $path_parts['filename']);
            }
            // Add route to collection.
            $this->collection->add($route_name, new Route(
              $route_path,
              [
                '_controller' => self::CONTROLLER,
                '_title' => $title,
                'filepath' => $element_path,
              ],
              self::PERMISSIONS
            ));
          }
        }
      }
      closedir($handle);
    }
  }

  /**
   * Returns route name representation of a string.
   *
   * @param string $string
   *   A string.
   *
   * @return string
   *   A route name representation of the string.
   */
  private function machinify($string) {
    return preg_replace("/[^a-z0-9_\-.]/", '', strtolower(preg_replace('/\s+/', '_', $string)));
  }

}