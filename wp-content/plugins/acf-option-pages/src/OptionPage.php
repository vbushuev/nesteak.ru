<?php

/*
 * Version: 1.2.0
 */

class ACFOP_OptionPage {

  public function add( $args ) {
    $page_title = $args['page_title'];
    $settings = $args['settings'];

    $args = array(
      'page_title'  => $page_title,
      'menu_title'  => $settings['menu_title'],
      'menu_slug'   => $settings['menu_slug'],
      'autoload'    => false,
    );

    $this->setCapability( $args, $settings );
    $this->setRedirect( $args, $settings );
    $this->setParentSlug( $args, $settings );
    $this->setPostID( $args, $settings );
    $this->setAutoload( $args, $settings );
    $this->setPosition( $args, $settings );
    $this->setIconUrl( $args, $settings );
    acf_add_options_page( $args );

  }

  public function setCapability( &$args, $settings ) {
    if( array_key_exists( 'capability', $settings ) && !empty($settings['capability']))   {
      $args['capability'] = $settings['capability'];
    }
  }

  public function setRedirect( &$args, $settings ) {
    if( array_key_exists( 'redirect', $settings ) && $settings['redirect'] != true ) {
      $args['redirect'] = false;
    }
  }

  public function setParentSlug( &$args, $settings ) {
    if( !array_key_exists( 'parent_slug', $settings )) {
      return;
    }
    if( !empty( $settings['parent_slug'] )) {
      $args['parent_slug'] = $settings['parent_slug'];
    }
  }

  public function setPostID( &$args, $settings ) {
    if( !array_key_exists( 'post_id', $settings )) {
      return;
    }
    if( !empty( $settings['post_id'] )) {
      $args['post_id'] = $settings['post_id'];
    }
  }

  public function setAutoload( &$args, $settings ) {
    if( !array_key_exists( 'autoload', $settings )) {
      return;
    }
    if( !empty( $settings['autoload'] )) {
      $args['autoload'] = $settings['autoload'];
    }
  }

  public function setPosition( &$args, $settings ) {
    if( !array_key_exists( 'position', $settings )) {
      return;
    }
    if( !empty( $settings['position'] )) {
      $args['position'] = $settings['position'];
    }
  }

  public function setIconUrl( &$args, $settings ) {
    if( !array_key_exists( 'icon_url', $settings )) {
      return;
    }
    if( !empty( $settings['icon_url'] )) {
      $args['icon_url'] = $settings['icon_url'];
    }
  }

}
