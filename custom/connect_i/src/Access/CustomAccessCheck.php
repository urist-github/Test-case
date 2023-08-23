<?php

namespace Drupal\connect_i\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Provides a custom access check based on user privacy settings.
 */
class CustomAccessCheck implements AccessInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a CustomAccessCheck object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match service.
   */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $route_match) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
    $this->routeMatch = $route_match;
  }

  /**
   * Checks access based on user privacy setting.
   *
   * - The superadmin (user ID 1) always has access.
   * - Other users will be denied access if the profile is marked as private.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access() {
    // Always allow access for user with ID 1.
    if ($this->currentUser->id() == 1) {
      return AccessResult::allowed()->addCacheContexts(['user']);
    }

    // Load the user entity from the current route.
    $user = $this->routeMatch->getParameter('user');

    // Ensure the user entity is loaded.
    if (!$user) {
      return AccessResult::forbidden();
    }

    // If the privacy setting for the user is set to "keep_private", forbid access.
    if ($user->field_privacy_setting->value === 'keep_private') {
      return AccessResult::forbidden()->addCacheContexts(['user']);
    }

    if ($this->currentUser->hasPermission('access user profiles')) {
      return AccessResult::allowed()->addCacheContexts(['user']);
    } else {
      return AccessResult::forbidden();
    }
  }
}
