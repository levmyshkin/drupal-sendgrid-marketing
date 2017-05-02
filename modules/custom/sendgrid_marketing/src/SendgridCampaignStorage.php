<?php

namespace Drupal\sendgrid_marketing;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface;

/**
 * Defines the storage handler class for Sendgrid campaign entities.
 *
 * This extends the base storage class, adding required special handling for
 * Sendgrid campaign entities.
 *
 * @ingroup sendgrid_marketing
 */
class SendgridCampaignStorage extends SqlContentEntityStorage implements SendgridCampaignStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(SendgridCampaignInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {sendgrid_campaign_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {sendgrid_campaign_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(SendgridCampaignInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {sendgrid_campaign_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('sendgrid_campaign_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
