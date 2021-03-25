<?php

namespace Drupal\drupal_aws_polly\Client;

use Aws\Polly\PollyClient;
use Aws\Credentials\Credentials;
use Drupal\file\Entity\File;

class AwsPollyClient {

  /**
   * The AWS Polly client object.
   * https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Polly.PollyClient.html
   *
   * @var \Aws\Polly\PollyClient
   */
  protected $client;

  /**
   * The returned audio stream.
   *
   * @var mixed
   */
  protected $audioStream;

  /**
   * The audio generated file.
   *
   * @var int
   */
  protected $fileId;

  /**
   * AwsPollyClient constructor.
   *
   * @param $api_keys
   *   AWS API keys.
   * @param $version
   *   Amazon Polly version.
   * @param $region
   *   Region.
   */
  public function __construct($api_keys, $version, $region) {
    $credentials = new Credentials($api_keys['aws_access_key'], $api_keys['aws_secret_key']);
    $this->client = new PollyClient([
      'version'     => $version,
      'credentials' => $credentials,
      'region'      => $region,
    ]);
  }

  /**
   * Set the audio stream value.
   *
   * @param $audioStream
   *   New audio stream.
   */
  private function setAudioStream($audioStream) {
    $this->audioStream = $audioStream;
  }

  /**
   * Returns the audioStreams array..
   */
  public function getAudioStream()
  {
    return $this->audioStream;
  }

  /**
   * Set the file id value.
   *
   * @param $fid
   *   New file id.
   */
  private function setFileId($fid) {
    $this->fileId = $fid;
  }

  /**
   * Returns the audioStreams array..
   */
  public function getFileId()
  {
    return $this->fileId;
  }

  /**
   * Call aws polly service and return an audio stream.
   *
   * @param array $body
   *   the request body.
   *   https://docs.aws.amazon.com/polly/latest/dg/API_SynthesizeSpeech.html
   *
   * @return \Drupal\drupal_aws_polly\Client\AwsPollyClient
   *   The audio stream.
   */
  public function synthesizeSpeech($body = []): AwsPollyClient {
    try {
      $result = $this->client->synthesizeSpeech($body);
      $this->setAudioStream($result->get('AudioStream')->getContents());
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addError('Something went wrong.');
    }

    return $this;
  }

  /**
   * Generates a file from a given audio source.
   *
   * @param $format
   *   Audio file format.
   * @param $audiosStream
   *   Audio stream
   * @param string $uri_schema
   *   Uri schema, either public or private.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function generateAudioFile($format, $uri_schema = "public"): AwsPollyClient {
    $folder = date('Y') . "-" . date('m') . "-" . date('d');
    $path = 'aws_polly/' . $folder . "/aws_polly_" . time() . "." . $format;
    $uri = ($uri_schema == "public") ? ("public://" . $path) : ("private://" . $path);
    $dir = ($uri_schema == "public") ? ("public://aws_polly/" . $folder) : ("private://aws_polly/" . $folder);

    if (!file_exists($dir)) {
      mkdir($dir, 0770, TRUE);
    }

    /** @var \Drupal\file\Entity\File $file */
    $file = File::create([
      'uid' => \Drupal::currentUser()->id(),
      'filename' => basename($path),
      'uri' => $uri,
      'status' => 1,
    ]);
    $file->save();

    file_put_contents($file->getFileUri(), $this->getAudioStream());
    $this->setFileId($file->id());

    return $this;
  }

}
