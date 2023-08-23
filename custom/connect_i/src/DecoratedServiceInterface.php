<?php

namespace Drupal\connect_i;

/**
 * Interface for the DecoratedService.
 */
interface DecoratedServiceInterface {

  /**
   * Fetches a node from the current route and returns its title.
   *
   * @return string|null
   *   The node title or NULL if the node is not found.
   */
  public function fetchNodeByRoute();

}
