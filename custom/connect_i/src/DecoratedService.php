<?php

namespace Drupal\connect_i;

use Drupal\contrib_module\OriginalService;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;

/**
 * Decorates the original service from contrib_module.
 */
class DecoratedService implements DecoratedServiceInterface {

  /**
   * The original service.
   *
   * @var \Drupal\contrib_module\OriginalService
   */
  protected $originalService;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new DecoratedService object.
   */
  public function __construct(OriginalService $original_service, EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $current_route_match) {
    $this->originalService = $original_service;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * Fetches a user based on the current route.
   *
   * @return string|null
   *   The result of the fetchUserByRoute() method from the original service.
   */
  public function fetchUserByRoute(): ?string {
    // Call the original method via the decorated service.
    return $this->originalService->fetchUserByRoute();
  }

  /**
   * Fetches a node from the current route and returns its title.
   *
   * @return string|null
   *    The label of the user entity if it exists in the current route,
   *    NULL otherwise.
   */
  public function fetchNodeByRoute(): ?string {
    $node = $this->currentRouteMatch->getParameter('node');
    if ($node instanceof NodeInterface) {
      return $node->label();
    }
    return NULL;
  }

}
