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

    public function new_project(){

        // Create post
        $new_post = array(
            'post_type' => 'project',
            'post_title' => $_POST['myproject'],
            'post_content' => $_POST['mymessage'],
        );

        // Insert the post into the database
        $post_id = wp_insert_post($new_post);

        update_post_meta( $post_id, 'github',  $_POST['mygithub'] );
        update_post_meta( $post_id, 'name',  $_POST['myname'] );
        update_post_meta( $post_id, 'organisation',  $_POST['myorganisation'] );
        update_post_meta( $post_id, 'email',  $_POST['myemail'] );

        wp_redirect(home_url()."?submitted");
        exit();

    }

}