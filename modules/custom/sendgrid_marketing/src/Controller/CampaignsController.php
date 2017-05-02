<?php
namespace Drupal\sendgrid_marketing\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for SendGrid Campaigns list.
 */
class CampaignsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $config = \Drupal::config('sendgrid_marketing.settings');
    $sendgrid_api_key = $message = $config->get('sendgrid_api_key');
    $sg = new \SendGrid($sendgrid_api_key);

    $query_params = json_decode('{"limit": 100, "offset": 0}');
    $response = $sg->client->campaigns()->get(null, $query_params);

    $data = $response->body();
    $data = json_decode($data);

    $header = array('id', 'title', 'Subscribers list', 'status', 'Statistics', 'Operations');
    $rows = array();
    foreach ($data->result as $campaign) {
      $statistics = '';
      if (in_array($campaign->status, ['Sent', 'Scheduled', 'In Progress'])) {
        $statistics = '<a target="_blank" href="https://sendgrid.com/marketing_campaigns/campaigns/' . $campaign->id . '/stats">Statistics</a>';
      }
      $operations_links = '';
     // $subscribers = _fyi_sendgrid_get_multiple_lists_by_ids($campaign->list_ids, $sg);
      $subscribers = implode(',', $subscribers);
      if (in_array($campaign->status, ['Draft'])) {
        $view_link = [
          'title' => t('View'),
          'href' => 'https://sendgrid.com/marketing_campaigns/campaigns/' . $campaign->id . '/edit#desktop-preview',
          'attributes' => [
            'target' => '_blank',
          ],
        ];
        $edit_link = [
          'title' => t('Edit'),
          'href' => 'admin/content/sendgrid-campaigns/' . $campaign->id . '/edit'
        ];
        $send_link = [
          'title' => t('Send'),
          'href' => 'admin/content/sendgrid-campaigns/' . $campaign->id . '/send',
          'attributes' => [
            'class' => 'use-ajax send-campaign-' . $campaign->id,
          ],
        ];

        //$operations_links = theme('links__ctools_dropbutton', ['links' => [$view_link, $edit_link, $send_link]]);
      }
      $rows[] = [
        $campaign->id,
        $campaign->title,
        $subscribers,
        '<div class="status-campaign-' . $campaign->id . '">' . $campaign->status . '</div>',
        $statistics,
        $operations_links,
      ];
    }

    $element = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#empty' => t('No SendGrid Campaigns found'),
    );
    return $element;
  }

}