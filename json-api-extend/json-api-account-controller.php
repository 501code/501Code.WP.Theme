<?php
/*
Controller name: Account
Controller description: Additional functionality to the JSON_API controller for the 501Code
*/
class JSON_API_ACCOUNT_Controller {

    public function logout(){
        session_start();
        session_unset();
        session_destroy();

        wp_redirect(home_url());
        exit();
    }

}