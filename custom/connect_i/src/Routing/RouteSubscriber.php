<?php

namespace Drupal\connect_i\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Alters existing routes for a specific use case.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The collection of routes for which to alter.
   */
  protected function alterRoutes(RouteCollection $collection): void {
    if ($route = $collection->get('entity.user.canonical')) {
      $route->addRequirements(['_custom_access_check' => 'TRUE']);
    }
  }
}
