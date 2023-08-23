<?php

namespace Drupal\contrib_module;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\UserInterface;

/**
 * Defines the OriginalService service class.
 */
class OriginalService {

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * Constructs an OriginalService service object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $currentRouteMatch
   *   The current route match service.
   */
  public function __construct(RouteMatchInterface $currentRouteMatch) {
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * Fetches the user entity from the current route.
   *
   * @return string|null
   *   The label of the user entity if it exists in the current route,
   *   NULL otherwise.
   */
  public function fetchUserByRoute(): ?string {
    $user = $this->currentRouteMatch->getParameter('user');
    if ($user instanceof UserInterface) {
      return $user->label();
    }
    return NULL;
  }
}
