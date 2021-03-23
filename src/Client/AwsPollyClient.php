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
  protected $audioStreams = [];

  /**
   * The audio generated file.
   *
   * @var int
   */
  protected $fileIds = [];

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
   * Adds a new audio stream to the audioStreams array.
   *
   * @param $audioStream
   *   New audio stream.
   */
  private function addAudioStream($audioStream) {
    array_push($this->audioStreams, $audioStream);
  }

  /**
   * Returns the audioStreams array..
   */
  public function getAudioStreams(): array
  {
    return $this->audioStreams;
  }

  /**
   * Adds a new file id to the fileIds array.
   *
   * @param $fid
   *   New file id.
   */
  private function addFileId($fid) {
    array_push($this->fileIds, $fid);
  }

  /**
   * Returns the audioStreams array..
   */
  public function getFileIds(): array
  {
    return $this->fileIds;
  }

  /**
   * Call aws polly service and return an audio stream.
   *
   * @param array $body
   *   the request body.
   *
   * @return mixed
   *   The audio stream.
   */
  public function synthesizeSpeech($body = []) {
    $result = $this->client->synthesizeSpeech($body);
    $this->addAudioStream($result->get('AudioStream')->getContents());

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
  public function generateAudioFile($format, $audiosStream, $uri_schema = "public") {
    $folder = date('Y') . "-" . date('m') . "-" . date('d');
    $path = 'aws_polly/' . $folder . "/aws_polly_" . time() . "." . $format;
    $uri = ($uri_schema == "public") ? ("public://" . $path) : ("private://" . $path);

    if (!file_exists($uri)) {
      mkdir($uri, 0770, TRUE);
    }

    /** @var \Drupal\file\Entity\File $file */
    $file = File::create([
      'uid' => \Drupal::currentUser()->id(),
      'filename' => basename($path),
      'uri' => $uri,
      'status' => 1,
    ])->save();

    file_put_contents($file->getFileUri(), $audiosStream);
    $this->addFileId($file->id());

    return $this;
  }

}
