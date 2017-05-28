<?php

namespace Drupal\sendgrid_marketing\Form;

use Drupal\sendgrid_marketing\SendgridCampaignStorageInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the comment multiple delete confirmation form.
 */
class ConfirmDeleteMultiple extends ConfirmFormBase {

  /**
   * The sendgrid campaign storage.
   *
   * @var \Drupal\sendgrid_marketing\SendgridCampaignStorageInterface
   */
  protected $campaignStorage;

  /**
   * An array of sendgrid campaigns to be deleted.
   */
  protected $campaigns;

  /**
   * Creates an new ConfirmDeleteMultiple form.
   *
   * @param \Drupal\sendgrid_marketing\SendgridCampaignStorageInterface $campaign_storage
   *   The campaign storage.
   */
  public function __construct(SendgridCampaignStorageInterface $campaign_storage) {
    $this->campaignStorage = $campaign_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('sendgrid_campaign')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sendgrid_campaign_multiple_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete these campaigns?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('sendgrid_marketing.sendgrid_campaigns_list');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete campaigns');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $edit = $form_state->getUserInput();

    $form['campaigns'] = [
      '#prefix' => '<ul>',
      '#suffix' => '</ul>',
      '#tree' => TRUE,
    ];
    // array_filter() returns only elements with actual values.
    $campaign_counter = 0;
    $ids = array_keys(array_filter($edit['campaigns']));
    $config = \Drupal::config('sendgrid_marketing.settings');
    $apiKey = $config->get('sendgrid_api_key');
    $sg = new \SendGrid($apiKey);

    foreach ($ids as $campaign_id) {
      $response = $sg->client->campaigns()->_($campaign_id)->get();
      if ($response->statusCode() == 200) {
        $body = $response->body();
        $body = json_decode($body);
        $campaign_name = $body->title;
      }
      else {
        $campaign_name = $campaign_id;
      }
      $form['campaigns'][$campaign_id] = [
        '#type' => 'hidden',
        '#value' => $campaign_id,
        '#prefix' => '<li>',
        '#suffix' => Html::escape($campaign_name) . '</li>'
      ];
      $campaign_counter++;
    }
    $form['operation'] = ['#type' => 'hidden', '#value' => 'delete'];

    if (!$campaign_counter) {
      drupal_set_message($this->t('There do not appear to be any campaigns to delete, or your selected campaign was deleted by another administrator.'));
      $form_state->setRedirect('sendgrid_marketing.sendgrid_campaigns_list');
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('confirm') && $form_state->getValue('campaigns')) {
      foreach ($form_state->getValue('campaigns') as $campaign_id) {
        $config = \Drupal::config('sendgrid_marketing.settings');
        $apiKey = $config->get('sendgrid_api_key');
        $sg = new \SendGrid($apiKey);

        $response = $sg->client->campaigns()->_($campaign_id)->delete();
      }

      // @todo delete campaings entities by reference field.
      // @todo get entity by Campaign ID.

      $count = count($form_state->getValue('campaigns'));
      $this->logger('sendgrid_marketing')->notice('Deleted @count campaigns.', ['@count' => $count]);
      drupal_set_message($this->formatPlural($count, 'Deleted 1 campaign.', 'Deleted @count campaigns.'));
    }
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}