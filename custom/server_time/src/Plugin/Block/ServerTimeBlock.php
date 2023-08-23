<?php

namespace Drupal\server_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a 'ServerTimeBlock' for anonymous users.
 *
 * @Block(
 *   id = "server_time_block",
 *   admin_label = @Translation("Server Time Block"),
 *   category = @Translation("Custom")
 * )
 */
class ServerTimeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Utilize lazy builder for dynamic content.
    return [
      '#lazy_builder' => ['server_time.get_server_time:getServerTime', []],
      '#create_placeholder' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf($account->isAnonymous());
  }
}
