INTRODUCTION
------------

This module provides an integration with the Amazon Polly service.

REQUIREMENTS
------------

This module requires the following SDK:

* aws/aws-sdk-php (https://github.com/aws/aws-sdk-php)

INSTALLATION
------------

* First, you'll have to install the AWS SDK for php, the recommended way to do that is using composer
  `composer require aws/aws-sdk-php` (^3.173 recommended).
  Check the project github repo for more information about the minimum requirements to run the SDK.
* Then, install the module as you would normally install a contributed Drupal module. Visit
  https://www.drupal.org/node/1897420 for further information.
* The service can be tested in the test page `/admin/config/development/aws_polly/test`

CONFIGURATION
-------------

* Before you begin, you need to sign up for an AWS account and retrieve your AWS credentials.
* Navigate to the settings page `Configuration > Web Services > AWS Polly Settings` or directly via the url `/admin/config/development/aws_polly/settings`
* Add the AWS Credentials, the version (https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.Polly.PollyClient.html) and the region (https://docs.aws.amazon.com/polly/latest/dg/NTTS-main.html)

FEATURES
-------------

* Converting a text into an audio file can be done by :
  * Using the AwsPollyService to create a new AWSPollyClient
  * Getting the audio stream from the AWS service by calling the client synthesizeSpeech method
  * Generate an audio file by calling the client generateAudioFile method.

* An example can be found in the submit method  of the form used to test the service `\Drupal\drupal_aws_polly\Form\AwsPollyTestForm`
