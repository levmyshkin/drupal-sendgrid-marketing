<?php

namespace Drupal\sendgrid_marketing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the SendGrid Campaigns overview administration form.
 */
class SendgridMarketingCampaignsList extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sendgrid_marketing_campaings_list';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('sendgrid_marketing.settings');
    $sendgrid_api_key = $message = $config->get('sendgrid_api_key');
    $sg = new \SendGrid($sendgrid_api_key);

    $query_params = json_decode('{"limit": 100, "offset": 0}');
    $response = $sg->client->campaigns()->get(null, $query_params);

    $data = $response->body();
    $data = json_decode($data);

    $header = array(
      'title' => $this->t('Title'),
      'subscribers_list' => $this->t('Subscribers list'),
      'status' =>  $this->t('status'),
      'statistics' => $this->t('Statistics'),
      'operations' => $this->t('Operations'),
    );
    $rows = array();
    foreach ($data->result as $campaign) {
      $statistics = '';
      if (in_array($campaign->status, ['Sent', 'Scheduled', 'In Progress'])) {
        $statistics = '<a target="_blank" href="https://sendgrid.com/marketing_campaigns/campaigns/' . $campaign->id . '/stats">Statistics</a>';
      }
      $operations_links = '';
      // $subscribers = _fyi_sendgrid_get_multiple_lists_by_ids($campaign->list_ids, $sg);
      $subscribers = implode(', ', $campaign->categories);
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
      $rows[$campaign->id] = [
        // @todo Add post date.
        'title' => $campaign->title,
        'subscribers_list' => $subscribers,
        'status' => $campaign->status,
        'statistics' => $statistics,
        // @todo Add operations later.
        'operations' => '',
      ];
    }

    $form['campaigns'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#empty' => t('No SendGrid Campaigns found'),
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
