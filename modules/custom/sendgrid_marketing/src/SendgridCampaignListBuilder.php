<?php

namespace Drupal\sendgrid_marketing;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Sendgrid campaign entities.
 *
 * @ingroup sendgrid_marketing
 */
class SendgridCampaignListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Sendgrid campaign ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\sendgrid_marketing\Entity\SendgridCampaign */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.sendgrid_campaign.edit_form', [
          'sendgrid_campaign' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
