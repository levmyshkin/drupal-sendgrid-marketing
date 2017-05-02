<?php
/**
 * @file
 * Contains Drupal\sendgrid_marketing\Form\SendgridMarketingConfigurationForm.
 */
namespace Drupal\sendgrid_marketing\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drpual\sendgrid_marketing\Form
 */
class SendgridMarketingConfigurationForm extends ConfigFormBase {
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
    return 'sendgrid_marketing_general_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sendgrid_marketing.settings');
    $form['sendgrid_api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('SendGrid API Key'),
      '#description' => t('See this page <a target="_blank" href="@link">@link</a>', array('@link' =>
        'https://app.sendgrid.com/settings/api_keys')),
      '#default_value' => $config->get('sendgrid_api_key'),
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
      ->set('sendgrid_api_key', $form_state->getValue('sendgrid_api_key'))
      ->save();
  }
}