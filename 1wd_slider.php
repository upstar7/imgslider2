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

  }


  add_action('wp_enqueue_scripts', 'fwds_styles');
  function fwds_styles() {

    wp_register_style('slidesjs_example', plugins_url('css/example.css', __FILE__));
    wp_enqueue_style('slidesjs_example');
    wp_register_style('slidesjs_fonts', plugins_url('css/font-awesome.min.css', __FILE__));
    wp_enqueue_style('slidesjs_fonts');

  }

  add_shortcode("1wd_slider", "fwds_display_slider");
  function fwds_display_slider() {

    $plugins_url = plugins_url();
  

    echo '<div class="container">
      <div id="slides">
        <img src="'.plugins_url( 'img/example-slide-1.jpg' , __FILE__ ).'" />
        <img src="'.plugins_url( 'img/example-slide-2.jpg' , __FILE__ ).'" />
        <img src="'.plugins_url( 'img/example-slide-3.jpg' , __FILE__ ).'" />
        <img src="'.plugins_url( 'img/example-slide-4.jpg' , __FILE__ ).'" />
        <a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
        <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
      </div>
    </div>';
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
    add_meta_box("fwds-slider-image","Slider Images",'fwds_view_slider_images_box',"slidesjs_slider","normal" );

  }

  function fwds_view_slider_images_box() {
    global $post;
    $gallery_images = get_post_meta($post->ID,"_fwds_gallery_images",true );
    //print_r($gallerry_images); exit;
    $gallery_images = ($gallery_images != '') ? json_decode( $gallery_images ) : array();
    
    // Use nonce for verification
    $html = '<input type="hidden" name="fwds_slider_box_nonce" value="'.wp_create_nonce( basename(__FILE__) ). '" />';
    // $html .= '';
    $html .= '
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
    ';

    echo $html;




  }


?>