<?php

namespace Drupal\drupal_aws_polly\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\drupal_aws_polly\Client\AwsPollyClient;

/**
 * Class AwsPollyService.
 */
class AwsPollyService {

  /**
   * The aws polly configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  public $config;

  /**
   * Constructs a new AwsPollyService.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('drupal_aws_polly.settings');
  }

  /**
   * instantiate a new aws polly client.
   */
  public function getAwsPollyClient() {
    $api_keys = [
      'aws_access_key' => $this->config->get('aws_access_key'),
      'aws_secret_key' => $this->config->get('aws_secret_key'),
    ];
    $version = $this->config->get('aws_version');
    $region = $this->config->get('aws_region');
    return new AwsPollyClient($api_keys, $version, $region);
  }

}
