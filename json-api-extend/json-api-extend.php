<?php
// Add a custom controller
add_filter('json_api_controllers', 'add_account_controller');
function add_account_controller($controllers) {
    // Corresponds to the class JSON_API_account_Controller
    $controllers[] = 'account';
    return $controllers;
}
//set path for custom controller
function set_account_controller_path() {
    return  plugin_dir_path(  dirname( __FILE__ )  ) . "json-api-extend/json-api-account-controller.php";
}
add_filter('json_api_account_controller_path', 'set_account_controller_path');
?>