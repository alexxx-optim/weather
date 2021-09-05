<?php

namespace Drupal\current_weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure current_weather settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'current_weather_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['current_weather.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['default_city_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Default city name'),
      '#default_value' => $this->config('current_weather.settings')
        ->get('default_city_name'),
    ];

    $form['default_country_code'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Default country code'),
      '#default_value' => $this->config('current_weather.settings')
        ->get('default_country_code'),
    ];

    $form['api_key'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('API key'),
      '#default_value' => $this->config('current_weather.settings')
        ->get('api_key'),
    ];

    $form['api_endpoint'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('API endpoint'),
      '#default_value' => $this->config('current_weather.settings')
        ->get('api_endpoint'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('current_weather.settings')
      ->set('default_city_name', $form_state->getValue('default_city_name'))
      ->set('default_country_code', $form_state->getValue('default_country_code'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('api_endpoint', $form_state->getValue('api_endpoint'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
