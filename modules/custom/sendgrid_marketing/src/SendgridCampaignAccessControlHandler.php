<?php

namespace Drupal\sendgrid_marketing;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Sendgrid campaign entity.
 *
 * @see \Drupal\sendgrid_marketing\Entity\SendgridCampaign.
 */
class SendgridCampaignAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished sendgrid campaign entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published sendgrid campaign entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit sendgrid campaign entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete sendgrid campaign entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add sendgrid campaign entities');
  }

}
