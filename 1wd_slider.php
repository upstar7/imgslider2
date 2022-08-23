<?php

  /*
  Plugin Name: 1WD Slider
  Plugin URI: http://1stwebdesigner.com/
  Description: Slider Component for WordPress
  Version: 1.0
  Author: Rakhitha Nimesh
  Author URI: http://1stwebdesigner.com/
  License: GPLv2 or later
  */
  function fwds_slider_activation() {
  }
  register_activation_hook(__FILE__, 'fwds_slider_activation');


  function fwds_slider_deactivation() {
  }
  register_deactivation_hook(__FILE__, 'fwds_slider_deactivation');




  add_action('wp_enqueue_scripts', 'fwds_scripts');
  function fwds_scripts() {

    wp_enqueue_script('jquery');

    wp_register_script('slidesjs_core', plugins_url('js/jquery.slides.min.js', __FILE__),array("jquery"));
    wp_enqueue_script('slidesjs_core');

    wp_register_script('slidesjs_init', plugins_url('js/slidesjs.initialize.js', __FILE__));
    wp_enqueue_script('slidesjs_init');

    $effect = (get_option('fwds_effect') == '') ? "slide" : get_option('fwds_effect');
    $interval = (get_option('fwds_interval') == '') ? 2000 : get_option('fwds_interval');
    $autoplay = (get_option('fwds_autoplay') == 'enabled') ? true : false;
    $playBtn = (get_option('fwds_playBtn') == 'enabled') ? true : false;

    $config_array = array(
      'effect' => $effect,
      'interval' => $interval,
      'autoplay' => $autoplay,
      'playBtn' => $playBtn
    );

    wp_localize_script('slidesjs_init', 'setting', $config_array);

  }


  add_action('wp_enqueue_scripts', 'fwds_styles');
  function fwds_styles() {

    wp_register_style('slidesjs_example', plugins_url('css/example.css', __FILE__));
    wp_enqueue_style('slidesjs_example');
    wp_register_style('slidesjs_fonts', plugins_url('css/font-awesome.min.css', __FILE__));
    wp_enqueue_style('slidesjs_fonts');

  }


  add_action('init', 'fwds_register_slider');

  function fwds_register_slider() {

    $labels = array(
      'menu_name' => _x( 'Sliders', 'slidesjs_slider' )
    );

    $args = array(
      'labels' => $labels, 
      'hierarchical' => true,
      'description' => 'Slideshows',
      'supports' => array('title', 'editor'),
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_manus' => true,
      'publicy_queryable' => true,
      'exclude_from_search' => false,
      'has_archive' => true,
      'query_var' => true,
      'can_export' => true,
      'rewrite' => true,
      'capability_type' => 'post'
    );

    register_post_type( 'slidesjs_slider', $args );
  }

  add_action( 'add_meta_boxes', 'fwds_slider_meta_box' );

  function fwds_slider_meta_box() {
    add_meta_box("fwds-slider-images","Slider Images",'fwds_view_slider_images_box',"slidesjs_slider","normal" );

  }

  function fwds_view_slider_images_box() {
    global $post;
    $gallery_images = get_post_meta($post->ID,"_fwds_gallery_images",true );
    //print_r($gallerry_images); exit;
    $gallery_images = ($gallery_images != '') ? json_decode( $gallery_images ) : array();
    
    // echo ($gallery_images);
    // Use nonce for verification
    $html = '<input type="hidden" name="fwds_slider_box_nonce" value="'.wp_create_nonce( basename(__FILE__) ). '" />';
    // $html .= '';
    $html = '
      <table class="form-table">
        <tbody>
          <tr>
            <th><label for="Upload Images">Image 1</label></th>
            <td><input id="fwds_slider_upload" type="text" name="gallery_img[]" value="'.$gallery_images[0].'" /></td>
          </tr>
          <tr>
            <th><label for="Upload Images">Image 2</label></th>
            <td><input id="fwds_slider_upload" type="text" name="gallery_img[]" value="'.$gallery_images[1].'" /></td>
          </tr>
          <tr>
            <th><label for="Upload Images">Image 3</label></th>
            <td><input id="fwds_slider_upload" type="text" name="gallery_img[]" value="'.$gallery_images[2].'" /></td>
          </tr>
          <tr>
            <th><label for="Upload Images">Image 4</label></th>
            <td><input id="fwds_slider_upload" type="text" name="gallery_img[]" value="'.$gallery_images[3].'" /></td>
          </tr>
          <tr>
            <th><label for="Upload Images">Image 5</label></th>
            <td><input id="fwds_slider_upload" type="text" name="gallery_img[]" value="'.$gallery_images[4].'" /></td>
          </tr>
        </tbody>
      </table>  
    ';
    echo $html;
  }

  add_action('save_post', 'fwds_save_slider_info');
  
  function fwds_save_slider_info($post_id) {
    //verify nonce

    if (!wp_verify_nonce($_POST['fwds_slider_box_nonce'], basename(__FILE__))) {
      return $post_id;
    }

    //check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    //check permission

    if ( 'slidesjs_slider' == $_POST['post_type'] && current_user_can('edit_post', $post_id) ) {
      // Save Slider Images
      //print_r($_POST['gallery_img']);exit;

      $gallery_images = (isset($_POST['gallery_img']) ? $POST_['gallery_img'] : '');

      $gallery_images = strip_tags(json_decode( $gallery_images ));

      update_post_meta($post_id, "_fwds_gallery_images", $gallery_images );
    
    } else {
      return $post_id;
    }
  }
  
  add_shortcode( "1wd_slider", "fwds_display_slider" );

  function fwds_display_slider($attr, $content) {
    
    extract(shortcode_atts(array(
      'id' => ''
    ), $attr));

    $gallery_images = get_post_meta( $id, "_fwds_gallery_images", true );

    $gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();

    $plugins_url = plugins_url();

    $html = '
      <div class="container">
        <div id="slides">
    ';

    foreach ($gallery_images as $gal_img) {
      # code...
      if ($gal_img != "") {

        $html .= '<img alt="" src="'.$gal_img.'" />';
      
      }
    }
    $html .='
        </div>
      </div>
    ';

    return $html;
  }

  //Creating The Admin Menu Page for Plugin Settings
  add_action( 'admin_menu', 'fwds_plugin_settings' );

  function fwds_plugin_settings() {
    add_menu_page( '1stWD Slider Settings', '1stWD Slider Settings', 'administrator', 'fwds_settings', 'fwds_display_settings' );
  }
  
  function fwds_display_settings() {
    
    $slide_effect = (get_option('fwds_effect') == 'slide') ? 'selected' : '';

    $fade_effect = (get_option('fwds_effect') == 'fade') ? 'selected' : '';

    $interval = (get_option('fwds_interval') != '') ? get_option('fwds_interval') : '2000';

    $autoplay = (get_option('fwds_autoplay') == 'enabled') ? 'checked' : '';

    $playBtn = (get_option('fwds_playBtn') == 'enabled') ? 'checked' : '';

    $html = '
      <div class= "wrap">
        <form action="option.php" method="post" name="options">
          <h2>Select Your Settings</h2>
          ' .wp_nonce_field('update-options') . '
          <table class="form-table" width="100%" cellpadding="10">
            <tbody>
              <tr>
                <td scope="row" align="left">
                  <label>Slider Effect</label>
                  <select name="fwds_effect">
                    <option value="slide">Slide</option>
                    <option value="fade">Fade</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td scope="row" align="left">
                  <label>Enable Play Button</label>
                  <input type="checkbox" name="fwds_playBtn" value="enabled" />
                </td>
              </tr>
              <tr>
                <td scope="row" align="left">
                  <label>Transition Interval</label>
                  <input type="text" name="fwds_interval" value="' . $interval . '" />
                </td>
              </tr>
            </tbody>
          </table> 
          <input type="hidden" name="action" value="update" />

          <input type="hidden" name="page_options" value="fwds_autoplay, fwds_effect, fwds_interval, fwds_playBtn" />

          <input type="submit" name="submit" value="Update" />
        </form>
      </div>  
      ';
    echo $html;  

  }







?>