<?php

namespace Drupal\drupal_aws_polly\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AwsPollySettingsForm.
 *
 * @package Drupal\drupal_aws_polly\Form
 */
class AwsPollySettingsForm extends ConfigFormBase {

  /**
   * Function buildForm.
   *
   * @param array $form
   *   Array given.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Second param is FormStateInterface param.
   *
   * @return array
   *   An array returned
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $config = $this->config('drupal_aws_polly.settings');

    $form['aws_access_key'] = [
      '#title' => $this->t('AWS Access Key'),
      '#type' => 'textfield',
      '#default_value' => $config->get('aws_access_key'),
      '#required' => TRUE,
    ];

    $form['aws_secret_key'] = [
      '#title' => $this->t('AWS Secret Key'),
      '#type' => 'textfield',
      '#default_value' => $config->get('aws_secret_key'),
      '#required' => TRUE,
    ];

    $form['aws_version'] = [
      '#title' => $this->t('AWS Version'),
      '#type' => 'textfield',
      '#default_value' => $config->get('aws_version') ?? '2016-06-10',
      '#required' => TRUE,
    ];

    $form['aws_region'] = [
      '#title' => $this->t('AWS Region'),
      '#type' => 'textfield',
      '#default_value' => $config->get('aws_region'),
      '#description' => "See http://docs.aws.amazon.com/general/latest/gr/rande.html for a list of available regions.",
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * Function validateForm.
   *
   * @param array $form
   *   Array given.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Second param is FormStateInterface param.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // parent::validateForm($form, $form_state);.
  }

  /**
   * Function submitForm.
   *
   * @param array $form
   *   Array given.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Second param is FormStateInterface param.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('drupal_aws_polly.settings')
      ->set('aws_access_key', $form_state->getValue('aws_access_key'))
      ->set('aws_secret_key', $form_state->getValue('aws_secret_key'))
      ->set('aws_version', $form_state->getValue('aws_version'))
      ->set('aws_region', $form_state->getValue('aws_region'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'drupal_aws_polly.settings',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'aws_polly_settings_form';
  }

}
