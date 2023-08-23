<?php

namespace Drupal\server_time;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Provides a service to get real-time server information.
 */
class ServerTimeService implements TrustedCallbackInterface {

  /**
   * Retrieves the server time message.
   *
   * @return array
   *   A renderable array containing the server time message.
   */
  public function getServerTime() {
    $current_time = time();
    $message = $current_time % 2 === 0
      ? t('Server time contains an even number')
      : t('Server time contains an odd number');

    return [
      '#markup' => $message,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['getServerTime'];
  }
}

