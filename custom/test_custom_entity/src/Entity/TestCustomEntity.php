<?php

namespace Drupal\test_custom_entity\Entity;


use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the Test entity class.
 *
 * @ContentEntityType(
 *   id = "test_custom_entity",
 *   label = @Translation("Test entity"),
 *   label_collection = @Translation("Test entity"),
 *   label_singular = @Translation("test entity"),
 *   label_plural = @Translation("test entity"),
 *   label_count = @PluralTranslation(
 *     singular = "@count test entity",
 *     plural = "@count test entities",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\UncacheableEntityPermissionProvider",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *   },
 *   admin_permission = "administer site configuration",
 *   base_table = "test_custom_entity",
 *   entity_keys = {
 *     "id" = "cid",
 *     "label" = "cid",
 *     "owner" = "uid",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/test-entity/{test_custom_entity}",
 *     "add-form" = "/test-entity/add",
 *     "edit-form" = "/test-entity/{test_custom_entity}/edit",
 *     "delete-form" = "/admin/content/test-entity/{test_custom_entity}/delete",
 *     "collection" = "/admin/content/test-custom-entity",
 *   }
 * )
 */
class TestCustomEntity extends ContentEntityBase implements EntityOwnerInterface {

  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the custom entity.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
        'settings' => [
          'size' => '60',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    $fields['referenced_page'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Referenced Basic Page'))
      ->setDescription(t('The node referenced by this entity.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler_settings', ['target_bundles' => ['page' => 'page']])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
