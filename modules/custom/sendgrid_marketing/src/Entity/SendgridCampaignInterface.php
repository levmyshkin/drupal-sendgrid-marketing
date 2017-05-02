<?php

namespace Drupal\sendgrid_marketing\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Sendgrid campaign entities.
 *
 * @ingroup sendgrid_marketing
 */
interface SendgridCampaignInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Sendgrid campaign name.
   *
   * @return string
   *   Name of the Sendgrid campaign.
   */
  public function getName();

  /**
   * Sets the Sendgrid campaign name.
   *
   * @param string $name
   *   The Sendgrid campaign name.
   *
   * @return \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   *   The called Sendgrid campaign entity.
   */
  public function setName($name);

  /**
   * Gets the Sendgrid campaign creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Sendgrid campaign.
   */
  public function getCreatedTime();

  /**
   * Sets the Sendgrid campaign creation timestamp.
   *
   * @param int $timestamp
   *   The Sendgrid campaign creation timestamp.
   *
   * @return \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   *   The called Sendgrid campaign entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Sendgrid campaign published status indicator.
   *
   * Unpublished Sendgrid campaign are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Sendgrid campaign is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Sendgrid campaign.
   *
   * @param bool $published
   *   TRUE to set this Sendgrid campaign to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   *   The called Sendgrid campaign entity.
   */
  public function setPublished($published);

  /**
   * Gets the Sendgrid campaign revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Sendgrid campaign revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   *   The called Sendgrid campaign entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Sendgrid campaign revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Sendgrid campaign revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   *   The called Sendgrid campaign entity.
   */
  public function setRevisionUserId($uid);

}
