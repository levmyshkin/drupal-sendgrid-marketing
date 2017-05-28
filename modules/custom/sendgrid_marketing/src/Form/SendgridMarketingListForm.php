<?php
/**
 * @file
 * Contains Drupal\sendgrid_marketing\Form\SendgridMarketingListForm.
 */
namespace Drupal\sendgrid_marketing\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Class SettingsForm.
 *
 * @package Drpual\sendgrid_marketing\Form
 */
class SendgridMarketingListForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sendgrid_marketing.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sendgrid_marketing_list_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sendgrid_marketing.settings');

    $form['sendgrid_sender_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Sender ID'),
      '#default_value' => $config->get('sendgrid_sender_id'),
      '#size' => 32,
      '#maxlength' => 255,
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>',
        array('@link' => 'https://sendgrid.com/marketing_campaigns/ui/senders')),
      '#required' => TRUE,
    );

    $form['sendgrid_list_id'] = array(
      '#type' => 'textfield',
      '#title' => t('List ID'),
      '#default_value' => $config->get('sendgrid_list_id'),
      '#size' => 32,
      '#maxlength' => 255,
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>',
        array('@link' => 'https://sendgrid.com/marketing_campaigns/contacts')),
      '#required' => TRUE,
    );

    $form['sendgrid_suppression_group_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Unsubscribe Group ID'),
      '#default_value' => $config->get('sendgrid_suppression_group_id'),
      '#size' => 32,
      '#maxlength' => 255,
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>',
        array('@link' => 'https://app.sendgrid.com/suppressions/advanced_suppression_manager')),
      '#required' => TRUE,
    );

    $form['sendgrid_ip_pool'] = array(
      '#type' => 'textfield',
      '#title' => t('IP Pool'),
      '#default_value' => $config->get('sendgrid_ip_pool'),
      '#size' => 32,
      '#maxlength' => 255,
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>',
        array('@link' => 'https://app.sendgrid.com/settings/ip_addresses')),
   );

    $form['sendgrid_category'] = array(
      '#type' => 'textfield',
      '#title' => t('Category'),
      '#default_value' => $config->get('sendgrid_category'),
      '#size' => 32,
      '#maxlength' => 255,
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>',
        array('@link' => 'https://app.sendgrid.com/statistics/category')),
    );

    $form['sendgrid_test_emails'] = array(
      '#type' => 'textfield',
      '#title' => t('Default Test Email addresses'),
      '#default_value' => $config->get('sendgrid_test_emails'),
      '#maxlength' => 2048,
      '#description' => t('Enter up to 10 addresses at once, separated by a comma.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->config('sendgrid_marketing.settings')
      ->set('sendgrid_sender_id', $form_state->getValue('sendgrid_sender_id'))
      ->set('sendgrid_list_id', $form_state->getValue('sendgrid_list_id'))
      ->set('sendgrid_suppression_group_id', $form_state->getValue('sendgrid_suppression_group_id'))
      ->set('sendgrid_ip_pool', $form_state->getValue('sendgrid_ip_pool'))
      ->set('sendgrid_category', $form_state->getValue('sendgrid_category'))
      ->set('sendgrid_test_emails', $form_state->getValue('sendgrid_test_emails'))
      ->save();
  }
}