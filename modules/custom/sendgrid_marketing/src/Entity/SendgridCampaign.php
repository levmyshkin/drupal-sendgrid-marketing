<?php

namespace Drupal\sendgrid_marketing\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Sendgrid campaign entity.
 *
 * @ingroup sendgrid_marketing
 *
 * @ContentEntityType(
 *   id = "sendgrid_campaign",
 *   label = @Translation("Sendgrid campaign"),
 *   handlers = {
 *     "storage" = "Drupal\sendgrid_marketing\SendgridCampaignStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\sendgrid_marketing\SendgridCampaignListBuilder",
 *     "views_data" = "Drupal\sendgrid_marketing\Entity\SendgridCampaignViewsData",
 *     "translation" = "Drupal\sendgrid_marketing\SendgridCampaignTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\sendgrid_marketing\Form\SendgridCampaignForm",
 *       "add" = "Drupal\sendgrid_marketing\Form\SendgridCampaignForm",
 *       "edit" = "Drupal\sendgrid_marketing\Form\SendgridCampaignForm",
 *       "delete" = "Drupal\sendgrid_marketing\Form\SendgridCampaignDeleteForm",
 *     },
 *     "access" = "Drupal\sendgrid_marketing\SendgridCampaignAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\sendgrid_marketing\SendgridCampaignHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "sendgrid_campaign",
 *   data_table = "sendgrid_campaign_field_data",
 *   revision_table = "sendgrid_campaign_revision",
 *   revision_data_table = "sendgrid_campaign_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer sendgrid campaign entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}",
 *     "add-form" = "/admin/structure/sendgrid_campaign/add",
 *     "edit-form" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/edit",
 *     "delete-form" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/delete",
 *     "version-history" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/revisions",
 *     "revision" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/revisions/{sendgrid_campaign_revision}/view",
 *     "revision_revert" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/revisions/{sendgrid_campaign_revision}/revert",
 *     "translation_revert" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/revisions/{sendgrid_campaign_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/sendgrid_campaign/{sendgrid_campaign}/revisions/{sendgrid_campaign_revision}/delete",
 *     "collection" = "/admin/structure/sendgrid_campaign",
 *   },
 *   field_ui_base_route = "sendgrid_campaign.settings"
 * )
 */
class SendgridCampaign extends RevisionableContentEntityBase implements SendgridCampaignInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the sendgrid_campaign owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Sendgrid campaign entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Sendgrid campaign entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['sendgrid_marketing_body'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Campaign body'))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRevisionable(TRUE);

    $fields['sendgrid_marketing_campaign_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('SendGrid Campaign ID'))
      ->setDescription(t('SendGrid Campaign ID.'));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Sendgrid campaign is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
