<?php

/**
 * Register and handle project user post type
 */

function project() {
    $labels = array(
        'name'               => _x( 'Projects', 'post type general name' ),
        'singular_name'      => _x( 'Project', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'project' ),
        'add_new_item'       => __( 'Add New project' ),
        'edit_item'          => __( 'Edit project' ),
        'new_item'           => __( 'New project' ),
        'all_items'          => __( 'All projects' ),
        'view_item'          => __( 'View projects' ),
        'search_items'       => __( 'Search projects' ),
        'not_found'          => __( 'No projects found' ),
        'not_found_in_trash' => __( 'No projects found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Projects',
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Defines project structure',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => array( 'title', 'editor', 'revisions', 'thumbnail'),
        'has_archive'   => true,
        'menu_icon'   => 'dashicons-clipboard'
    );
    register_post_type( 'project', $args );
}
add_action( 'init', 'project' );


//Project type meta-data
add_action( 'add_meta_boxes', 'project_details_box' );
function project_details_box() {
    add_meta_box(
        'project_details_box',
        __( 'Project details', 'myplugin_textdomain' ),
        'project_details_box_content',
        'project',
        'side',
        'high'
    );
}
function project_details_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'project_details_box_content_nonce' );

    $github = get_post_meta( get_the_ID(), 'github', true);
    $name = get_post_meta( get_the_ID(), 'name', true);
    $email = get_post_meta( get_the_ID(), 'email', true);
    $organisation = get_post_meta( get_the_ID(), 'organisation', true);
    ?>

    <input type="text" value="<?php echo $github;?>" name="github" placeholder="Github username">
    <br />
    <input type="text" value="<?php echo $name;?>" name="name" placeholder="Name">
    <br />
    <input type="text" value="<?php echo $email;?>" name="email" placeholder="Email">
    <br />
    <input type="text" value="<?php echo $organisation;?>" name="organisation" placeholder="Organisation">

    <?php
}

add_action( 'save_post', 'project_details_box_save' );

function project_details_box_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( !wp_verify_nonce( $_POST['project_details_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;
    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    update_post_meta( $post_id, 'github', $_POST['mygithub'] );
    update_post_meta( $post_id, 'name', $_POST['myname'] );
    update_post_meta( $post_id, 'email', $_POST['myemail'] );
    update_post_meta( $post_id, 'project', $_POST['mysubject'] );
    update_post_meta( $post_id, 'description', $_POST['mymessage'] );
    update_post_meta( $post_id, 'organisation', $_POST['myorganisation'] );
}