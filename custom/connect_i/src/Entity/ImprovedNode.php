<?php

namespace Drupal\connect_i\Entity;

use Drupal\node\Entity\Node;

/**
 * Extends the core Node class to add additional functionality.
 *
 * The ImprovedNode class provides a method to return a trimmed version
 * of the node's title.
 */
class ImprovedNode extends Node {

  /**
   * Gets a trimmed version of the node title.
   *
   * This method returns the first 10 characters of the node's title.
   *
   * @return string
   *   The trimmed title.
   */
  public function getTrimmedTitle() {
    // Use the core getTitle method and then trim it.
    return substr($this->getTitle(), 0, 10);
  }

}

