<?php 
/*
 Plugin Name: VW Kids Pro Posttype
 lugin URI: https://www.vwthemes.com/
 Description: Creating new post type for VW Kids Pro Theme.
 Author: VW Themes
 Version: 1.0
 Author URI: https://www.vwthemes.com/
*/

define( 'VW_KIDS_TOY_POSTTYPE_VERSION', '1.0' );
add_action( 'init', 'vw_kids_pro_posttype_create_post_type' );

function vw_kids_pro_posttype_create_post_type() {

  register_post_type( 'clients',
    array(
      'labels' => array(
        'name' => __( 'Clients','vw-kids-pro-posttype' ),
        'singular_name' => __( 'Clients','vw-kids-pro-posttype' )
      ),
      'capability_type' => 'post',
      'menu_icon'  => 'dashicons-businessman',
      'public' => true,
      'supports' => array(
        'title',
        'editor',
        'thumbnail'
      )
    )
  );
}

/*---------------------------------- Clients section -------------------------------------*/
/* Adds a meta box to the clients editing screen */
function vw_kids_pro_posttype_bn_clients_meta_box() {
  add_meta_box( 'vw-kids-pro-posttype-clients-meta', __( 'Enter Details', 'vw-kids-pro-posttype' ), 'vw_kids_pro_posttype_bn_clients_meta_callback', 'clients', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
  add_action('admin_menu', 'vw_kids_pro_posttype_bn_clients_meta_box');
}

/* Adds a meta box for custom post */
function vw_kids_pro_posttype_bn_clients_meta_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'vw_kids_pro_posttype_posttype_clients_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
  $desigstory = get_post_meta( $post->ID, 'vw_kids_pro_posttype_clients_desigstory', true );
  ?>
  <div id="clients_custom_stuff">
    <table id="list">
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-1">
          <td class="left">
            <?php _e( 'Designation', 'vw-kids-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="vw_kids_pro_posttype_clients_desigstory" id="vw_kids_pro_posttype_clients_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

/* Saves the custom meta input */
function vw_kids_pro_posttype_bn_metadesig_save( $post_id ) {
  if (!isset($_POST['vw_kids_pro_posttype_posttype_clients_meta_nonce']) || !wp_verify_nonce($_POST['vw_kids_pro_posttype_posttype_clients_meta_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Save desig.
  if( isset( $_POST[ 'vw_kids_pro_posttype_clients_desigstory' ] ) ) {
    update_post_meta( $post_id, 'vw_kids_pro_posttype_clients_desigstory', sanitize_text_field($_POST[ 'vw_kids_pro_posttype_clients_desigstory']) );
  }

}

add_action( 'save_post', 'vw_kids_pro_posttype_bn_metadesig_save' );

/*---------------------------------- clients shortcode --------------------------------------*/
function vw_kids_pro_posttype_clients_func( $atts ) {
  $clients = '';
  $clients = '<div class="row all-clients">';
  $query = new WP_Query( array( 'post_type' => 'clients') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=clients');
  while ($new->have_posts()) : $new->the_post();

        $post_id = get_the_ID();
         $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        $url = $thumb['0'];
        $custom_url ='';
        
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $tdegignation= get_post_meta($post_id,'vw_kids_pro_posttype_clients_desigstory',true);
        if(get_post_meta($post_id,'meta-clients-url',true !='')){$custom_url =get_post_meta($post_id,'meta-clients-url',true); } else{ $custom_url = get_permalink(); }
        $clients .= '

            <div class="our_clients_outer col-lg-4 col-md-4 col-sm-6">
              <div class="clients_inner">
                <div class="row hover_border">
                  <div class="col-md-12">
                     <img class="classes-img" src="'.esc_url($thumb_url).'" alt="attorney-thumbnail" />
                    <h4><a href="'.esc_url($custom_url).'">'.esc_html(get_the_title()) .'</a></h4>
                    <div class="tdesig">'.$tdegignation.'</div>
                    <div class="short_text">'.$excerpt.'</div>
                  </div>
                </div>
              </div>
            </div>';
    if($k%2 == 0){
      $clients.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $clients = '<h2 class="center">'.esc_html__('Post Not Found','vw_kids_pro_posttype').'</h2>';
  endif;
  return $clients;
}

add_shortcode( 'vw-kids-clients', 'vw_kids_pro_posttype_clients_func' );
