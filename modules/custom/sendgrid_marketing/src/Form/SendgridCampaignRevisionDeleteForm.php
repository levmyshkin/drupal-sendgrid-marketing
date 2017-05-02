<?php

namespace Drupal\sendgrid_marketing\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Sendgrid campaign revision.
 *
 * @ingroup sendgrid_marketing
 */
class SendgridCampaignRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The Sendgrid campaign revision.
   *
   * @var \Drupal\sendgrid_marketing\Entity\SendgridCampaignInterface
   */
  protected $revision;

  /**
   * The Sendgrid campaign storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $SendgridCampaignStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new SendgridCampaignRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->SendgridCampaignStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('sendgrid_campaign'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sendgrid_campaign_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the revision from %revision-date?', ['%revision-date' => format_date($this->revision->getRevisionCreationTime())]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.sendgrid_campaign.version_history', ['sendgrid_campaign' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sendgrid_campaign_revision = NULL) {
    $this->revision = $this->SendgridCampaignStorage->loadRevision($sendgrid_campaign_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->SendgridCampaignStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Sendgrid campaign: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    drupal_set_message(t('Revision from %revision-date of Sendgrid campaign %title has been deleted.', ['%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.sendgrid_campaign.canonical',
       ['sendgrid_campaign' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {sendgrid_campaign_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.sendgrid_campaign.version_history',
         ['sendgrid_campaign' => $this->revision->id()]
      );
    }
  }

}
