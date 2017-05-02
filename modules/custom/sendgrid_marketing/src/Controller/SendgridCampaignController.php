<?php

namespace Drupal\sendgrid_marketing\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface;

/**
 * Class SendgridCampaignController.
 *
 *  Returns responses for Sendgrid campaign routes.
 *
 * @package Drupal\sendgrid_marketing\Controller
 */
class SendgridCampaignController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Sendgrid campaign  revision.
   *
   * @param int $sendgrid_campaign_revision
   *   The Sendgrid campaign  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($sendgrid_campaign_revision) {
    $sendgrid_campaign = $this->entityManager()->getStorage('sendgrid_campaign')->loadRevision($sendgrid_campaign_revision);
    $view_builder = $this->entityManager()->getViewBuilder('sendgrid_campaign');

    return $view_builder->view($sendgrid_campaign);
  }

  /**
   * Page title callback for a Sendgrid campaign  revision.
   *
   * @param int $sendgrid_campaign_revision
   *   The Sendgrid campaign  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($sendgrid_campaign_revision) {
    $sendgrid_campaign = $this->entityManager()->getStorage('sendgrid_campaign')->loadRevision($sendgrid_campaign_revision);
    return $this->t('Revision of %title from %date', ['%title' => $sendgrid_campaign->label(), '%date' => format_date($sendgrid_campaign->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Sendgrid campaign .
   *
   * @param \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface $sendgrid_campaign
   *   A Sendgrid campaign  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(SendgridCampaignInterface $sendgrid_campaign) {
    $account = $this->currentUser();
    $langcode = $sendgrid_campaign->language()->getId();
    $langname = $sendgrid_campaign->language()->getName();
    $languages = $sendgrid_campaign->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $sendgrid_campaign_storage = $this->entityManager()->getStorage('sendgrid_campaign');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $sendgrid_campaign->label()]) : $this->t('Revisions for %title', ['%title' => $sendgrid_campaign->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all sendgrid campaign revisions") || $account->hasPermission('administer sendgrid campaign entities')));
    $delete_permission = (($account->hasPermission("delete all sendgrid campaign revisions") || $account->hasPermission('administer sendgrid campaign entities')));

    $rows = [];

    $vids = $sendgrid_campaign_storage->revisionIds($sendgrid_campaign);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\sendgrid_marketing\SendgridCampaignInterface $revision */
      $revision = $sendgrid_campaign_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $sendgrid_campaign->getRevisionId()) {
          $link = $this->l($date, new Url('entity.sendgrid_campaign.revision', ['sendgrid_campaign' => $sendgrid_campaign->id(), 'sendgrid_campaign_revision' => $vid]));
        }
        else {
          $link = $sendgrid_campaign->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.sendgrid_campaign.translation_revert', ['sendgrid_campaign' => $sendgrid_campaign->id(), 'sendgrid_campaign_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.sendgrid_campaign.revision_revert', ['sendgrid_campaign' => $sendgrid_campaign->id(), 'sendgrid_campaign_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.sendgrid_campaign.revision_delete', ['sendgrid_campaign' => $sendgrid_campaign->id(), 'sendgrid_campaign_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['sendgrid_campaign_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
