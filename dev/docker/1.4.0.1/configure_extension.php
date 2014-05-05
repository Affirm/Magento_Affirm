<?php

require '../app/Mage.php';

function readSettings($filename) {
  $config = json_decode(file_get_contents($filename), TRUE);

  // this indirection is necessary to receive the |secret_key_field_name| from 
  // the json file
  $extensionSettings = $config["settings"];

  // the field name of the secret key is read from the config so that this function doesn't depend
  // on the name of the config field
  $nameOfSecretKeyField = $config["secret_key_field_name"];
  $plaintextSecretKey = $extensionSettings[$nameOfSecretKeyField];
  $extensionSettings[$nameOfSecretKeyField] = Mage::helper('core')->encrypt($plaintextSecretKey);

  return $extensionSettings;
}

function configureAffirmMagentoExtension($settings) {
  $configManager = new Mage_Core_Model_Config();

  $scope = "default";
  $scopeId = 0;

  foreach ($settings as $key => $value) {
    $configManager->saveConfig($key, $value, $scope, $scopeId);
  }

}

function main() {
  $app = Mage::app('default');

  ini_set('display_errors', '1');
  error_reporting(E_ALL);

  $filename = "./config.json";
  $settings = readSettings($filename);
  configureAffirmMagentoExtension($settings);
}

main();

?>
