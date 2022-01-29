<?php

/**
 * Plugin Name:       Elash Slider
 * Description:       Just sliding images
 * Version:           1.0.0
 * Author:            Ayman Elash
 * Author Uri: facebook.com/wordpressace
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type for Slider
function elash_setup_post_type()
{
    register_post_type('slide', ['public' => true, 'label' => 'Slides', 'supports' => ['title', 'thumbnail'], 'status' => 'publish']);
}
add_action('init', 'elash_setup_post_type');


// Activation Hook
function elash_activation()
{

    elash_setup_post_type();

    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'elash_activation');



/**
 * ... enqueue plugin scripts.
 */
function elash_load_plugin_scripts()
{
    if (is_admin()) {
        return;
    } else {
        wp_enqueue_style('slick', plugin_dir_url(__FILE__) . 'css/slick.css');
        wp_enqueue_style('slick-theme', plugin_dir_url(__FILE__) . 'css/slick-theme.css');
        wp_enqueue_style('elash_main', plugin_dir_url(__FILE__) . 'css/elash_main.css');

        wp_enqueue_script(
            'slick',
            plugin_dir_url(__FILE__) . 'js/slick.min.js',
            ['jquery'],
            true
        );
        wp_enqueue_script(
            'elash',
            plugin_dir_url(__FILE__) . 'js/elash_main.js',
            ['jquery'],
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'elash_load_plugin_scripts');



// Adding the shortcode to display the slider

add_shortcode('elashslider', 'elash_main_func');


// getting the slider images and titles
function elash_main_func($atts = [], $content = null)
{

    $content .= '<div class="slider-container">
        <div class="slider">';
    $args = array(
        'post_type' => 'slide',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'ASC'
    );
    $slides = new WP_Query($args);
    if ($slides->have_posts()) :
        while ($slides->have_posts()) :
            $slides->the_post();
            $content .= '<div class="slide" style="position:relative"><img src="' . get_the_post_thumbnail_url() . '" width="100%" /><div class="el-overlay"><h2 class="el-slider-title">'.get_the_title().'</h2></div></div>';
        endwhile;
    endif;
    $content .= '</div></div>';

    return $content;
}





/**
 * Deactivation hook.
 */
function elash_deactivate()
{
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type('slide');
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'elash_deactivate');
