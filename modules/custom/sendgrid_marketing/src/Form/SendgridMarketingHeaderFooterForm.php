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
class SendgridMarketingHeaderFooterForm extends ConfigFormBase {
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
    return 'sendgrid_marketing_header_footer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sendgrid_marketing.settings');

    $form['sendgrid_html_header'] = array(
      '#type' => 'text_format',
      '#title' => t('HTML Header'),
      '#format' => 'full_html',
      '#default_value' => $config->get('sendgrid_html_header'),
      '#description' => t('Header or Hooter must contain link with [unsubscribe] URL'),
    );

    $form['sendgrid_plain_text_header'] = array(
      '#type' => 'textarea',
      '#title' => t('Plain text Header'),
      '#default_value' => $config->get('sendgrid_plain_text_header'),
      '#description' => t('Plain text Header or Hooter must contain [unsubscribe] token'),
    );

    $form['sendgrid_html_footer'] = array(
      '#type' => 'text_format',
      '#title' => t('HTML Footer'),
      '#format' => 'full_html',
      '#default_value' => $config->get('sendgrid_html_footer'),
      '#description' => t('Header or Hooter must contain link with [unsubscribe] URL'),
    );

    $form['sendgrid_plain_text_footer'] = array(
      '#type' => 'textarea',
      '#title' => t('Plain text Footer'),
      '#default_value' => $config->get('sendgrid_plain_text_footer'),
      '#description' => t('Plain text Header or Hooter must contain [unsubscribe] token'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $values = $form_state->getValues();
    $html_unsubscribe_exists = FALSE;
    if (strpos($values['sendgrid_html_header']['value'], '[unsubscribe]') > -1) {
      $html_unsubscribe_exists = TRUE;
    }
    if (strpos($values['sendgrid_html_footer']['value'], '[unsubscribe]') > -1) {
      $html_unsubscribe_exists = TRUE;
    }
    if ($html_unsubscribe_exists == FALSE) {
      $form_state->setErrorByName('sendgrid_html_header', t('Header or Hooter must contain link with [unsubscribe] URL'));
    }

    $plain_unsubscribe_link_exists = FALSE;
    if (strpos($values['sendgrid_plain_text_header'], '[unsubscribe]') > -1) {
      $plain_unsubscribe_link_exists = TRUE;
    }
    if (strpos($values['sendgrid_plain_text_footer'], '[unsubscribe]') > -1) {
      $plain_unsubscribe_link_exists = TRUE;
    }
    if ($plain_unsubscribe_link_exists == FALSE) {
      $form_state->setErrorByName('sendgrid_plain_text_header', t('Plain text Header or Hooter must contain [unsubscribe] token'));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $html_header = $form_state->getValue('sendgrid_html_header');
    $html_footer = $form_state->getValue('sendgrid_html_footer');
    $this->config('sendgrid_marketing.settings')
      ->set('sendgrid_html_header', $html_header['value'])
      ->set('sendgrid_plain_text_header', $form_state->getValue('sendgrid_plain_text_header'))
      ->set('sendgrid_html_footer', $html_footer['value'])
      ->set('sendgrid_plain_text_footer', $form_state->getValue('sendgrid_plain_text_footer'))
      ->save();
  }
}