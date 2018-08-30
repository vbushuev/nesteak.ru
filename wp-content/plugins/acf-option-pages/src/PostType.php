<?php

class ACFOP_PostType {

  public $key;
  public $name;
  public $args;
  public $settings;

  public function __construct() {
    $this->args = array(
      'description' => $this->settings['description'],
      'public' => true,
      'publicly_queryable' => true,
      'exclude_from_search' => false,
      'show_ui' => true,
      'show_in_nav_menus' => true,
      'show_in_menu' => true,
      'show_in_admin_bar' => true,
      'menu_position' => 80,
      'menu_icon' => 'dashicons-admin-tools',
      'hierarchical' => false,
      'supports' => array('title', 'editor'),
      'register_meta_box_cb' => '',
      'taxonomies' => array(),
      'has_archive' => true,
      'permalink_epmask' => '',
      'rewrite' => true,
      'query_var' => true,
      'can_export' => true,
      'show_in_rest' => false,
      'rest_base' => '',
      'rest_controller_class' => '',
    );
  }

  public function applySetting( $settingKey ) {
    if( array_key_exists( $settingKey, $this->settings )) {
      return $this->args[ $settingKey ] = $this->settings[ $settingKey ];
    }
  }

  public function add( $args ) {
    if ( post_type_exists( $args['key'] ) ) {
      return;
    }

    $this->settings = array();
    if( array_key_exists( 'settings', $args )) {
      $this->settings = $args['settings'];
    }
    $this->key = $args['key'];
    $this->name = $args['name'];

    // set main display label
    $this->args['label'] = $this->name . 's';

    $this->setLabels();
    $this->setBooleanOptions();
    $this->setMenuOptions();
    $this->setAdvancedOptions();
    $this->setTaxonomies();
    $this->setSupports();
    $this->setRestOptions();
    $this->setRewriteOptions();

    // debug args  var_dump( $this->args );
    register_post_type( $this->key, $this->args, 50 );
  }

  public function setRewriteOptions() {
    if( !array_key_exists( 'rewrite', $this->settings )) {
      $this->args['rewrite'] = false;
      return;
    }

    $rewrite = array(
      'slug' => $this->settings['slug'],
      'with_front' => $this->settings['with_front'],
      'feeds' => $this->settings['feeds'],
      'pages' => $this->settings['pages'],
      'ep_mask' => $this->settings['ep_mask'],
    );

    $this->args['rewrite'] = $rewrite;
  }

  public function setTaxonomies() {
    if( array_key_exists( 'taxonomies', $this->settings ) && isset( $this->settings['taxonomies'] )) {
      $this->args['taxonomies'] = $this->parseTextareaToArray( $this->settings['taxonomies'] );
    }
  }

  public function setRestOptions() {
    $this->setBooleanOption( 'show_in_rest' );
    $this->applySetting('rest_base');
    $this->applySetting('rest_controller_class');
  }

  public function setSupports() {
    $this->applySetting('supports');
  }

  public function parseTextareaToArray( $textareaContent ) {
    $a = array();
    $textareaContent = str_replace( ' ', '', $textareaContent );
    $a = explode( ',', $textareaContent );
    return $a;
  }

  public function setAdvancedOptions() {
    $this->applySetting('permalink_epmask');
    $this->applySetting('register_meta_box_cb');
  }

  public function setMenuOptions() {
    $this->applySetting('menu_position');
    $this->applySetting('menu_icon');
  }

  public function setBooleanOptions() {
    $this->setBooleanOption( 'public' );
    $this->setBooleanOption( 'publicly_queryable' );
    $this->setBooleanOption( 'exclude_from_search' );
    $this->setBooleanOption( 'show_ui' );
    $this->setBooleanOption( 'show_in_nav_menus' );
    $this->setBooleanOption( 'show_in_menu' );
    $this->setBooleanOption( 'show_in_admin_bar' );
    $this->setBooleanOption( 'hierarchical' );
    $this->setBooleanOption( 'has_archive' );
    $this->setBooleanOption( 'query_var' );
    $this->setBooleanOption( 'can_export' );
    $this->setBooleanOption( 'show_in_rest' );
  }

  public function setBooleanOption( $option ) {
    if( empty( $this->settings[ $option ] )) {
      return;
    }
    if( $this->settings[ $option ] ) {
      $this->args[ $option ] = true;
    } else {
      $this->args[ $option ] = false;
    }
  }

  public function setLabels() {
    $labels = array();
    $labelKeys = array(
      'name', 'singular_name', 'add_new', 'add_new_item', 'edit_item', 'new_item', 'view_item', 'search_items', 'not_found', 'not_found_in_trash', 'parent_item_colon', 'all_items', 'archives', 'insert_into_item', 'uploaded_to_this_item', 'featured_image', 'set_featured_image', 'remove_featured_image', 'use_featured_image', 'menu_name', 'filter_items_list', 'items_list_navigation', 'items_list', 'name_admin_bar'
    );

    // set each label if available
    foreach( $labelKeys as $l ) {
      if( array_key_exists('lbl_' .$l, $this->settings) && isset( $this->settings['lbl_' .$l] )) {
        $labels[ $l ] = $this->settings['lbl_' .$l];
      }
    }

    $this->args['labels'] = $labels;
  }

}
