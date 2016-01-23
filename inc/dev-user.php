<?php

/**
* Register and handle dev user post type
*/

function developer() {
$labels = array(
    'name'               => _x( 'Developers', 'post type general name' ),
    'singular_name'      => _x( 'Developer', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'developer' ),
    'add_new_item'       => __( 'Add New developer' ),
    'edit_item'          => __( 'Edit developer' ),
    'new_item'           => __( 'New developer' ),
    'all_items'          => __( 'All developers' ),
    'view_item'          => __( 'View developers' ),
    'search_items'       => __( 'Search developers' ),
    'not_found'          => __( 'No developers found' ),
    'not_found_in_trash' => __( 'No developers found in the Trash' ),
    'parent_item_colon'  => '',
    'menu_name'          => 'Developers',
);
$args = array(
    'labels'        => $labels,
    'description'   => 'Defines developer structure',
    'public'        => true,
    'menu_position' => 6,
    'supports'      => array( 'title', 'editor', 'revisions', 'thumbnail'),
    'has_archive'   => true,
    'menu_icon'   => 'dashicons-clipboard'
);
register_post_type( 'developer', $args );
}
add_action( 'init', 'developer' );


//Developer type meta-data
add_action( 'add_meta_boxes', 'developer_details_box' );
function developer_details_box() {
    add_meta_box(
        'developer_details_box',
        __( 'Developer details', 'myplugin_textdomain' ),
        'developer_details_box_content',
        'developer',
        'side',
        'high'
    );
}
function developer_details_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'developer_details_box_content_nonce' );

    $login = get_post_meta( get_the_ID(), 'login', true);
    $name = get_post_meta( get_the_ID(), 'name', true);
    $id = get_post_meta( get_the_ID(), 'id', true);
    $avatar_url = get_post_meta( get_the_ID(), 'avatar_url', true);
    $company = get_post_meta( get_the_ID(), 'company', true);
    $blog = get_post_meta( get_the_ID(), 'blog', true);
    $location = get_post_meta( get_the_ID(), 'location', true);
    ?>

    <input type="text" value="<?php echo $login;?>" name="login" placeholder="login">
    <br />
    <input type="text" value="<?php echo $name;?>" name="name" placeholder="name">
    <br />
    <input type="text" value="<?php echo $id;?>" name="id" placeholder="id">
    <br />
    <input type="text" value="<?php echo $avatar_url;?>" name="avatar_url" placeholder="avatar_url">
    <br />
    <input type="text" value="<?php echo $company;?>" name="company" placeholder="company">
    <br />
    <input type="text" value="<?php echo $blog;?>" name="blog" placeholder="blog">
    <br />
    <input type="text" value="<?php echo $location;?>" name="location" placeholder="location">

    <?php
}

add_action( 'save_post', 'developer_details_box_save' );

function developer_details_box_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !wp_verify_nonce( $_POST['developer_details_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;
    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    update_post_meta( $post_id, 'login', $_POST['login'] );
    update_post_meta( $post_id, 'name', $_POST['name'] );
    update_post_meta( $post_id, 'id', $_POST['id'] );
    update_post_meta( $post_id, 'avatar_url', $_POST['avatar_url'] );
    update_post_meta( $post_id, 'company', $_POST['company'] );
    update_post_meta( $post_id, 'blog', $_POST['blog'] );
    update_post_meta( $post_id, 'location', $_POST['location'] );
}

function create_edit_user($user){

    //check if user already exists
    $args = array(
        'posts_per_page'   => 1,
        'offset'           => 0,
        'category'         => '',
        'category_name'    => '',
        'orderby'          => 'date',
        'order'            => 'DESC',
        'include'          => '',
        'exclude'          => '',
        'meta_key'         => '',
        'meta_value'       => '',
        'post_type'        => 'developer',
        'post_mime_type'   => '',
        'post_parent'      => '',
        'author'	   => '',
        'post_status'      => 'draft',
        'suppress_filters' => true
    );


$current_users = get_posts( $args );


    if(sizeof($current_users)>0){
        $user_id = $current_users[0]->ID;
    }else {

        print '<div class="alert alert-success" role="alert">Successfully registered! You will recieve an email invitation in a few hours!</div>';
        // Create user
        $new_user = array(
            'post_type' => 'developer',
            'post_title' => $user->login
        );

        // Insert the post into the database
        $user_id = wp_insert_post($new_user);
    }

    $_SESSION['login'] = $user->login;
    $_SESSION['name'] = $user->name;
    $_SESSION['company'] = $user->company;
    $_SESSION['email'] = $user->email;

    update_post_meta( $user_id, 'login', $user->login );
    update_post_meta( $user_id, 'name', $user->name );
    update_post_meta( $user_id, 'id', $user->id );
    update_post_meta( $user_id, 'avatar_url', $user->avatar_url );
    update_post_meta( $user_id, 'company', $user->company );
    update_post_meta( $user_id, 'blog', $user->blog );
    update_post_meta( $user_id, 'location', $user->location );
    update_post_meta( $user_id, 'email', $user->email );

}

function github_login()
{
    define('OAUTH2_CLIENT_ID', 'f9db322587301b744d28');
    define('OAUTH2_CLIENT_SECRET', 'fc71bc68f07ada823d38f3a3b53b15b5650b027b');
    $authorizeURL = 'https://github.com/login/oauth/authorize';
    $tokenURL = 'https://github.com/login/oauth/access_token';
    $apiURLBase = 'https://api.github.com/';


    // Start the login process by sending the user to Github's authorization page
    if(get('action') == 'login') {
        // Generate a random hash and store in the session for security
        $_SESSION['state'] = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
        unset($_SESSION['access_token']);
        $params = array(
            'client_id' => OAUTH2_CLIENT_ID,
            'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
            'scope' => 'user',
            'state' => $_SESSION['state']
        );
        // Redirect the user to Github's authorization page
        //header('Location: ' . $authorizeURL . '?' . http_build_query($params));
        //die();
        //wp_redirect());
        //exit();
        echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$authorizeURL . '?' . http_build_query($params).'">';
        die();
    }
    // When Github redirects the user back here, there will be a "code" and "state" parameter in the query string
    if(get('code')) {
        // Verify the state matches our stored state
        if(!get('state') || $_SESSION['state'] != get('state')) {
            echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$_SERVER['PHP_SELF'].'">';
            die();
        }
        // Exchange the auth code for a token
        $token = apiRequest($tokenURL, array(
            'client_id' => OAUTH2_CLIENT_ID,
            'client_secret' => OAUTH2_CLIENT_SECRET,
            'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
            'state' => $_SESSION['state'],
            'code' => get('code'),
            'User-Agent' => '501code'
        ));
        $_SESSION['access_token'] = $token->access_token;
        echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$_SERVER['PHP_SELF'].'">"';
        die();
    }
    if(session('access_token')) {
        $user = apiRequest($apiURLBase . 'user');
        //echo '<h4>' . $user->name . '</h4>';
        print '<a href="'.home_url().'/api/account/logout/" style="float: right;" class="tiny-link"><i class="fa fa-github"></i> Logout</a>';

        create_edit_user($user);
    } else {
        print '<a href="?action=login"  style="float: right;" class="tiny-link"><i class="fa fa-github"></i> Login/Signup With Github</a>';
    }

}
function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $headers[] = 'Accept: application/json';
    $headers[] = 'User-Agent: 501code';
    if(session('access_token'))
        $headers[] = 'Authorization: Bearer ' . session('access_token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return json_decode($response);
}
function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}
function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}