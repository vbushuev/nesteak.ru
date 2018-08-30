<?php

/*
  Plugin Name: ACF Option Pages
  Plugin URI: http://goldhat.ca/plugins/acf-option-pages/
  Description: Creates options pages using ACF interface and ACF functions
  Version: 1.1.0
  Author: Joel Milne, GoldHat Group
  Author URI: http://goldhat.ca
  Text Domain: acf-option-pages
  License: GPLv2 or later
 */

new ACF_Option_Pages;

class ACF_Option_Pages {

  public function __construct() {
    require('src/PostType.php');
    require('src/OptionPage.php');
    add_action('init', array( $this, 'includeAcfFields' ));
    add_action('init', array( $this, 'addOptionsPagePostType' ));
    add_action('init', array( $this, 'addRegisteredOptionsPages' ));
  }

  public function includeAcfFields() {
    require('assets/acf/fields.php');
  }

  public function addOptionsPagePostType() {
    $pt = new ACFOP_PostType;
    $args = array(
      'key' => 'acf_option_page',
      'name' => 'Option Page',
      'settings' => array(
        'lbl_name'          => 'Option Pages',
        'lbl_add_new'       => 'Add Option Page',
        'lbl_add_new_item'  => 'Add Option Page',
      )
    );
    $pt->add( $args );
  }

  public function addRegisteredOptionsPages() {
    $ops = $this->getOptionPages();
    foreach( $ops as $opPost ) {
      $this->registerOptionPage( $opPost );
    }
  }

  public function registerOptionPage( $post ) {
    $op = new ACFOP_OptionPage;
    $fields = get_fields( $post->ID );

    $args = array(
      'page_title' => $post->post_title,
      'settings' => $fields,
    );

    $op->add( $args );
  }

  public function getOptionPages() {
    return get_posts( array(
      'post_type'     => 'acf_option_page',
      'meta_key'	    => 'is_subpage',
      'orderby'			  => 'meta_value_num',
	    'order'				  => 'DESC',
      'numberposts'   => -1,
    ));
  }

}
