<?php

/**
 * Plugin Name:       Elash Slider
 * Description:       Just sliding images
 * Version:           1.0.0
 * Author:            Ayman Elash
 * Author Uri: facebook.com/wordpressace
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elslider
 * Domain Path:       /languages
 */

class ElashSliderPlugin
{

    // Activation Hook
    function elash_activation()
    {

        elash_setup_post_type();

        flush_rewrite_rules();
    }



    function __construct()
    {
        add_action('admin_menu', array($this, 'AdminPage'));
        add_action('admin_init', array($this, 'settings'));

        add_action('wp_enqueue_scripts', array($this, 'elash_load_plugin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'mw_enqueue_color_picker'));

        add_action('init', array($this, 'elash_setup_post_type'));


        add_shortcode('elashslider', array($this, 'elash_main_func'));

        register_activation_hook(__FILE__, array($this, 'elash_activation'));
        register_deactivation_hook(__FILE__, array($this, 'elash_deactivate'));
    }

    function settings()
    {
        add_settings_section('elsection', esc_attr__('Slider Options', 'elslider'), null, 'elash_slider');

        add_settings_field('el_slidesToShow', esc_attr__('Slides To Show', 'elslider'), array($this, 'el_slidesToShowHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_slidesToShow', array("sanitize_callback" => "sanitize_text_field"));

        add_settings_field('el_slidesToScroll', esc_attr__('Slides to scroll', 'elslider'), array($this, 'el_slidesToScrollHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_slidesToScroll', array("sanitize_callback" => "sanitize_text_field"));

        add_settings_field('el_title', esc_attr__('Show Title ?', 'elslider'), array($this, 'el_titleHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_title', array("sanitize_callback" => "sanitize_text_field"));

        add_settings_field('el_autoplay', esc_attr__('Autoplay ?', 'elslider'), array($this, 'el_autoplayHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_autoplay', array("sanitize_callback" => "sanitize_text_field"));

        add_settings_field('el_speed', esc_attr__('Autoplay speed in ms', 'elslider'), array($this, 'el_speedHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_speed', array("sanitize_callback" => "sanitize_text_field"));

        add_settings_field('el_overlay', esc_attr__('Show Overlay ?', 'elslider'), array($this, 'el_overlayHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_overlay', array("sanitize_callback" => "sanitize_text_field", "default" => "1"));

        add_settings_field('el_overlayColor', esc_attr__('Overlay Color', 'elslider'), array($this, 'el_overlayColorHtml'), 'elash_slider', 'elsection');
        register_setting('eloptions', 'el_overlayColor', array("sanitize_callback" => "sanitize_text_field"));
    }

    function el_slidesToShowHtml()
    {
?>
        <input type="number" name="el_slidesToShow" value="<?php echo esc_attr(get_option('el_slidesToShow')); ?>" />
    <?php
    }
    function el_slidesToScrollHtml()
    {
    ?>
        <input type="number" name="el_slidesToScroll" value="<?php echo esc_attr(get_option('el_slidesToScroll')); ?>" />
    <?php
    }
    function el_autoplayHtml()
    {
    ?>
        <input type="checkbox" name="el_autoplay" value="1" <?php checked(esc_attr(get_option('el_autoplay'), '1')); ?> />
    <?php
    }
    function el_titleHtml()
    {
    ?>
        <input type="checkbox" name="el_title" value="1" <?php checked(esc_attr(get_option('el_title'), '1')); ?> />
    <?php
    }
    function el_overlayHtml()
    {
    ?>
        <input type="checkbox" name="el_overlay" value="1" <?php checked(esc_attr(get_option('el_overlay'), '1')); ?> />
    <?php
    }
    function el_speedHtml()
    {
    ?>
        <input type="number" name="el_speed" value="<?php echo esc_attr(get_option('el_speed')); ?>" />
    <?php
    }
    function el_overlayColorHtml()
    {
    ?>
        <input type="text" name="el_overlayColor" value="<?php echo esc_attr(get_option('el_overlayColor')); ?>" class="my-color-field" />
    <?php
    }

    function AdminPage()
    {
        add_menu_page('Elash Slider Options', 'Elash Slider Options', 'manage_options', 'elash_slider', array($this, 'AdminPageHtml'), 'dashicons-images-alt2', 110);
    }

    function AdminPageHtml()
    {

        settings_errors();

    ?>
        <div class="wrap">
            <h1><?php esc_attr__('Elash Slider', 'elslider') ?></h1>
            <form method="POST" action="options.php">
                <?php
                settings_fields('eloptions');
                do_settings_sections('elash_slider');
                submit_button();
                ?>
                <h3>Please use this shortcode to display the slider <strong>[elashslider]</strong></h3>
            </form>
        </div>
<?php
    }




    // Register Custom Post Type for Slider
    function elash_setup_post_type()
    {
        register_post_type('slide', ['public' => true, 'label' => 'Elash Slider', 'supports' => ['title', 'thumbnail'], 'status' => 'publish']);
    }

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

    function mw_enqueue_color_picker($hook_suffix)
    {
        // first check that $hook_suffix is appropriate for your admin page
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('color-picker', plugin_dir_url(__FILE__) . 'js/color-picker.js', array('wp-color-picker'), false, true);
    }


    // getting the slider images and titles
    function elash_main_func($atts = [], $content)
    {

        if (get_option('el_title') == "1") {
            $show_title = "true";
        } else {
            $show_title = "false";
        }

$content .= '<div class="slider-container"><div class="slider" data-slides="' . esc_attr(get_option('el_slidesToShow')) . '" data-scroll="' . esc_attr(get_option('el_slidesToScroll')) . '" data-play="' . esc_attr(get_option('el_autoplay')) . '" data-spedd="' . get_option('el_speed') . '" >';        $args = array(
            'post_type' => 'slide',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'ASC'
        );
        $slides = new WP_Query($args);
        if ($slides->have_posts()) :
            while ($slides->have_posts()) :
                $slides->the_post();
                $content .= '<div class="slide" style="position:relative"><img src="' . get_the_post_thumbnail_url() . '" width="100%" /><div class="el-overlay ' . (esc_attr(get_option('el_overlay')) == '1' ? '' : 'd-none') . '" style="background-color:' . esc_attr(get_option('el_overlayColor')) . '">' . (($show_title == "true") ? '<h2 class="el-slider-title">' . get_the_title() . '</h2>' : '')  . '</div></div>';
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
}

$elashSliderPlugin = new ElashSliderPlugin();
