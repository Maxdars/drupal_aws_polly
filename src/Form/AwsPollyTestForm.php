<?php

namespace Drupal\drupal_aws_polly\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\drupal_aws_polly\Services\AwsPollyService;

/**
 * Class AwsPollyTestForm.
 */
class AwsPollyTestForm extends FormBase {

  /**
   * The AWS Polly manager.
   *
   * @var \Drupal\drupal_aws_polly\Services\AwsPollyService
   */
  protected $awsPollyManager;

  /**
   * AwsPollyTestForm constructor.
   * @param AwsPollyService $awsPollyManager
   */
  public function __construct(AwsPollyService $awsPollyManager) {
    $this->awsPollyManager = $awsPollyManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('drupal_aws_polly.manager')
    );
  }

  /**
   * Function Get Form Id.
   */
  public function getFormId() {
    return 'aws_polly_test_form';
  }

  /**
   * Function build Form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    $form['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text to convert.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Convert to audio'),
    ];

    return $form;
  }

  /**
   * Function Submittion Form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $text = $form_state->getValue('text');

    // Instantiate a new AWS Polly Client.
    $awsPollyClient = $this->awsPollyManager->getAwsPollyClient();

    // Prepare the request body.
    // https://docs.aws.amazon.com/polly/latest/dg/API_SynthesizeSpeech.html
    $request_body = [
      'OutputFormat' => 'mp3',
      'LanguageCode' => 'en-US',
      'Text'         => $text,
      'TextType'     => 'text',
      'VoiceId'      => 'Joey',
    ];

    // Convert the text into audio stream, then create the corresponding file and returns its id.
    $fid = $awsPollyClient->synthesizeSpeech($request_body)
      ->generateAudioFile("mp3","public")
      ->getFileId();

    // Load the generated file.
    $file = File::load($fid);

    \Drupal::messenger()->addMessage($this->t("Text converted successfully, generated file : @url .", [
      '@url' => file_create_url($file->getFileUri()),
    ]));

    $form_state->setRebuild(FALSE);
  }

}
