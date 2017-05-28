<?php

namespace Drupal\sendgrid_marketing\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

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

    // @todo see example CommentAdminOverview.php.

    // Build an 'Update options' form.
    $form['options'] = [
      '#type' => 'details',
      '#title' => $this->t('Remove campaigns'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['container-inline']],
    ];

    $options['delete'] = $this->t('Delete the selected campaigns');

    $form['options']['operation'] = [
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#title_display' => 'invisible',
      '#options' => $options,
      '#default_value' => 'delete',
    ];

    $form['options']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
    );

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
      // @todo Replace List ids with List names, add cache for getting List names.
      $subscribers = implode(', ', $campaign->list_ids);
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

    if (empty($rows)) {
      $url = Url::fromRoute('sendgrid_marketing.general_settings');
      $link = \Drupal::l(t('configuration page'), $url);
      $form['empty-message'] = [
        '#markup' => t('No SendGrid Campaigns found. Check your SendGrid settings on ') .
          $link->getGeneratedLink(),
        '#weight' => -9,
      ];
    }

    $form['campaigns'] = array(
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $rows,
      '#empty' => t('No SendGrid Campaigns found'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('campaigns', array_diff($form_state->getValue('campaigns'), [0]));
    // We can't execute any 'Update options' if no comments were selected.
    if (count($form_state->getValue('campaigns')) == 0) {
      $form_state->setErrorByName('', $this->t('Select one or more campaigns to perform the update on.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $operation = $form_state->getValue('operation');
    $campaigns_ids = $form_state->getValue('campaigns');

    foreach ($campaigns_ids as $campaigns_id) {
      $test = 'adsf';
    }
    drupal_set_message($this->t('The update has been performed.'));
    $form_state->setRedirect('sendgrid_marketing.sendgrid_campaigns_list');
  }
}
