<?php
/**
* Plugin Name: unity-plugin
* Plugin URI: https://github.com/GitHub-Pavel
* Description: Plugin for unity child theme
* Version: 0.1
* Author: Pavel Khatkevich
* Author URI: https://github.com/GitHub-Pavel
**/

define("UNITY_CHILD_PLUGIN_TEXT_DOMAIN", "unity_child_plugin_text_domain");

add_action( 'init', 'unity_plugin_post_types' );
function unity_plugin_post_types(){
  register_post_type( 'real_estate', [
    'label'  => null,
    'labels' => [
      'name'               => __('Real estates', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'singular_name'      => __('Real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'add_new'            => __('Add real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'add_new_item'       => __('Adding real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'edit_item'          => __('Edit real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'new_item'           => __('New real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'view_item'          => __('View real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'search_items'       => __('Search real estate', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'not_found'          => __('Real estate not found', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
      'not_found_in_trash' => __('Real estate not fount in trash'),
      'parent_item_colon'  => '',
      'menu_name'          => __('Real estates', UNITY_CHILD_PLUGIN_TEXT_DOMAIN),
    ],
    'description'         => 'Real estates', 
    'public'              => true,
    'show_in_menu'        => null,
    'show_in_rest'        => null,
    'rest_base'           => null,
    'menu_position'       => null,
    'menu_icon'           => null,
    'hierarchical'        => false,
    'supports'            => ['title'],
    'taxonomies'          => [],
    'has_archive'         => false,
    'rewrite'             => true,
    'query_var'           => true,
  ]);
}