<?php

namespace Drupal\connect_i\Plugin\Block;

use Drupal\connect_i\DecoratedService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\connect_i\DecoratedServiceInterface;

/**
 * Provides a 'NodeListingBlock' block.
 *
 * This block plugin will list nodes sorted by their type and display them in a table.
 *
 * @Block(
 *   id = "node_listing_block",
 *   admin_label = @Translation("Node listing by type"),
 *   category = @Translation("Custom")
 * )
 */
class NodeListingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * Used for managing entity operations, such as loading nodes.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger service.
   *
   * Used for logging errors and other events.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The decorated service.
   *
   * @var \Drupal\connect_i\DecoratedServiceInterface
   */
  protected $decoratedService;


  /**
   * Constructs a new NodeListingBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\connect_i\DecoratedServiceInterface $decorated_service
   *   The decorated service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, LoggerChannelFactoryInterface $logger_factory, AccountProxyInterface $current_user, DecoratedServiceInterface $decorated_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('custom_node_listing');
    $this->currentUser = $current_user;
    $this->decoratedService = $decorated_service;
  }


  /**
   * {@inheritdoc}
   *
   * Creates an instance of the class.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
      $container->get('current_user'),
      $container->get('connect_i.decorated_service')
    );
  }


  /**
   * {@inheritdoc}
   *
   * Builds the block content.
   * This method loads the nodes, sorts them by type and prepares a table for display.
   */
  public function build(): array {
    $groupedNodes = [];

    // Get the current user.
    $current_user = $this->currentUser;

    // Attempt to get the nids of published nodes.
    try {
      $query = $this->entityTypeManager->getStorage('node')->getQuery()
        ->condition('status', 1)
        ->sort('type', 'ASC')
        ->addTag('node_access');  // This tag allows the Node Access API to alter the query.

      if (!$current_user->hasPermission('access content')) {
        // If the user doesn't have permission, then return an empty array to display nothing.
        return [];
      }

      $nids = $query->execute();

      // Load the nodes by their nids.
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

      // Group nodes by type.
      foreach ($nodes as $node) {
        $groupedNodes[$node->bundle()][] = $node;
      }
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }

    $rows = [];
    foreach ($groupedNodes as $type => $nodesOfType) {
      $typeRowCount = count($nodesOfType);

      $firstNode = array_shift($nodesOfType);
      $rows[] = [
        'data' => [
          ['data' => $type, 'rowspan' => $typeRowCount], // This cell will span multiple rows based on the number of nodes of this type.
          $firstNode->label()
        ]
      ];

      // Add the rest of the nodes after the first one.
      foreach ($nodesOfType as $node) {
        $rows[] = [$node->label() . ' ' . $this->decoratedService->fetchUserByRoute()];
      }
    }

    return [
      '#theme' => 'table',
      '#header' => [t('Node Type'), t('Node Title')],
      '#rows' => $rows,
      '#attached' => [
        // Sorry for this code, it's just for better table display.
        'html_head' => [
          [
            [
              '#type' => 'html_tag',
              '#tag' => 'style',
              '#value' => '
                table {
                  border-collapse: collapse;
                  width: 100%;
                }
                table, th, td {
                  border: 1px solid black;
                }
                th, td {
                  padding: 8px 12px;
                }
              '
            ],
            'table_style'
          ]
        ]
      ]
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Determines the access to the block.
   * Users with "view published content" permission can view the block.
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
}
