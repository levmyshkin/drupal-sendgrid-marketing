<?php

namespace Drupal\sendgrid_marketing;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface SendgridCampaignStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Sendgrid campaign revision IDs for a specific Sendgrid campaign.
   *
   * @param \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface $entity
   *   The Sendgrid campaign entity.
   *
   * @return int[]
   *   Sendgrid campaign revision IDs (in ascending order).
   */
  public function revisionIds(SendgridCampaignInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Sendgrid campaign author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Sendgrid campaign revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface $entity
   *   The Sendgrid campaign entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(SendgridCampaignInterface $entity);

  /**
   * Unsets the language for all Sendgrid campaign with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
