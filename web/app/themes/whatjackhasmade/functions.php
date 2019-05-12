<?php
/**
 * WhatJackHasMade WordPress Theme
 */

if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });

    add_filter('template_include', function ($template) {
        return get_stylesheet_directory() . '/static/no-timber.html';
    });

    return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('components');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site
{
    /** Add timber support. */
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'theme_supports'));
        add_action('after_setup_theme', array($this, 'legacy_functions'));
        add_filter('timber_context', array($this, 'add_to_context'));
        add_filter('upload_mimes', array($this, 'cc_mime_types'));
        add_action('init', array($this, 'register_blocks'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_my_menu'));
        add_action('acf/init', array($this, 'my_acf_init'));
        add_image_size('ratio', 300, 300, true);
        add_image_size('featured_xs', 350, 175, true);
        add_image_size('featured_sm', 450, 225, true);
        add_image_size('featured_md', 768, 384, true);
        add_image_size('featured_lg', 1300, 640, true);
        add_image_size('featured_xl', 1920, 1080, true);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content', 'wpautop');
        remove_filter('the_excerpt', 'wpautop');
        add_filter('post_thumbnail_html', array($this, 'remove_image_size_attributes'));
        add_filter('image_send_to_editor', array($this, 'remove_image_size_attributes'));
        apply_filters('rocket_cache_reject_wp_rest_api', false);
        parent::__construct();
    }

    /** This is where you can register custom post types. */
    public function register_post_types()
    {
        register_taxonomy_for_object_type('category', 'case');
        register_taxonomy_for_object_type('post_tag', 'case');
        register_post_type('case',
            array(
                'labels' => array(
                    'name' => __('Case Study', 'case'),
                    'singular_name' => __('Case Study', 'case'),
                    'add_new' => __('Add New', 'case'),
                    'add_new_item' => __('Add New Case Study', 'case'),
                    'edit' => __('Edit', 'case'),
                    'edit_item' => __('Edit Case Study', 'case'),
                    'new_item' => __('New Case Study', 'case'),
                    'view' => __('View Case Study', 'case'),
                    'view_item' => __('View Case Study', 'case'),
                    'search_items' => __('Search Case Study', 'case'),
                    'not_found' => __('No Case Studies found', 'case'),
                    'not_found_in_trash' => __('No Case Studies found in Trash', 'case'),
                ),
                'public' => true,
                'hierarchical' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-media-document',
                'can_export' => true,
                'taxonomies' => array(
                    'post_tag',
                    'category',
                ),
            ));

        register_taxonomy_for_object_type('category', 'event');
        register_taxonomy_for_object_type('post_tag', 'event');
        register_post_type('event',
            array(
                'labels' => array(
                    'name' => __('Event', 'event'),
                    'singular_name' => __('Event', 'event'),
                    'add_new' => __('Add New', 'event'),
                    'add_new_item' => __('Add New Event', 'event'),
                    'edit' => __('Edit', 'event'),
                    'edit_item' => __('Edit Event', 'event'),
                    'new_item' => __('New Event', 'event'),
                    'view' => __('View Event', 'event'),
                    'view_item' => __('View Event', 'event'),
                    'search_items' => __('Search Event', 'event'),
                    'not_found' => __('No Events found', 'event'),
                    'not_found_in_trash' => __('No Events found in Trash', 'event'),
                ),
                'public' => true,
                'hierarchical' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-tickets',
                'can_export' => true,
                'taxonomies' => array(
                    'post_tag',
                    'category',
                ),
            ));

        register_taxonomy_for_object_type('category', 'inspiration');
        register_taxonomy_for_object_type('post_tag', 'inspiration');
        register_post_type('inspiration',
            array(
                'labels' => array(
                    'name' => __('Inspiration', 'inspiration'),
                    'singular_name' => __('Inspiration', 'inspiration'),
                    'add_new' => __('Add New', 'inspiration'),
                    'add_new_item' => __('Add New Inspiration', 'inspiration'),
                    'edit' => __('Edit', 'inspiration'),
                    'edit_item' => __('Edit Inspiration', 'inspiration'),
                    'new_item' => __('New Inspiration', 'inspiration'),
                    'view' => __('View Inspiration', 'inspiration'),
                    'view_item' => __('View Inspiration', 'inspiration'),
                    'search_items' => __('Search Inspiration', 'inspiration'),
                    'not_found' => __('No Inspirations found', 'inspiration'),
                    'not_found_in_trash' => __('No Inspirations found in Trash', 'inspiration'),
                ),
                'public' => true,
                'hierarchical' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-images-alt2',
                'can_export' => true,
                'taxonomies' => array(
                    'post_tag',
                    'category',
                ),
            ));

        register_taxonomy_for_object_type('category', 'review');
        register_taxonomy_for_object_type('post_tag', 'review');
        register_post_type('review',
            array(
                'labels' => array(
                    'name' => __('Review', 'review'),
                    'singular_name' => __('Review', 'review'),
                    'add_new' => __('Add New', 'review'),
                    'add_new_item' => __('Add New Review', 'review'),
                    'edit' => __('Edit', 'review'),
                    'edit_item' => __('Edit Review', 'review'),
                    'new_item' => __('New Review', 'review'),
                    'view' => __('View Review', 'review'),
                    'view_item' => __('View Review', 'review'),
                    'search_items' => __('Search Review', 'review'),
                    'not_found' => __('No Reviews found', 'review'),
                    'not_found_in_trash' => __('No Reviews found in Trash', 'review'),
                ),
                'public' => true,
                'hierarchical' => true,
                'has_archive' => true,
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                ),
                'show_in_rest' => true,
                'menu_icon' => 'dashicons-format-status',
                'can_export' => true,
                'taxonomies' => array(
                    'post_tag',
                    'category',
                ),
            ));
    }

    public function register_my_menu()
    {
        register_nav_menus(array(
            'primary' => 'Primary Menu',
            'footer_one' => 'First Footer Menu',
            'footer_two' => 'Second Footer Menu',
            'footer_three' => 'Third Footer Menu',
            'footer_four' => 'Fourth Footer Menu',
            'secondary' => 'Secondary Menu',
        ));
    }

    public function register_blocks()
    {
        if (function_exists('acf_register_block')) {

            $blockies = array('dribbble', 'github', 'hero', 'intro', 'presentations', 'row', 'testimonials', 'youtube');
            $blockiesIcons = array('admin-customizer', 'screenoptions', 'align-center', 'editor-alignleft', 'format-chat', 'align-left', 'format-quote', 'format-video');

            $blockies = array_combine($blockies, $blockiesIcons);

            foreach ($blockies as $b => $v) {
                acf_register_block(array(
                    'description' => __('A custom' . $b . 'block.'),
                    'icon' => $v,
                    'mode' => 'edit',
                    'name' => $b,
                    'supports' => array(
                        'align' => array('wide', 'full'),
                    ),
                    'title' => __(ucfirst($b)),
                ));
            }
        }
    }

    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context($context)
    {
        $context['menu_primary'] = new TimberMenu('primary');
        $context['menu_footer_one'] = new TimberMenu('footer_one');
        $context['menu_footer_two'] = new TimberMenu('footer_two');
        $context['menu_footer_three'] = new TimberMenu('footer_three');
        $context['menu_footer_four'] = new TimberMenu('footer_four');
        $context['menu_secondary'] = new TimberMenu('secondary');

        $context['site'] = $this;
        return $context;
    }

    public function theme_supports()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page();
        }

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support(
            'html5', array(
                'gallery',
            )
        );
        add_theme_support(
            'post-formats', array(
                'aside',
                'image',
                'video',
                'link',
                'gallery',
            )
        );
        add_theme_support('menus');

        add_theme_support('align-wide');
        add_theme_support('disable-custom-colors');
        add_theme_support('disable-custom-font-sizes');
    }

    public function cc_mime_types($mimes)
    {
        $mimes['webp'] = 'image/webp';
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function remove_image_size_attributes($html)
    {
        return preg_replace('/(width|height)="\d*"/', '', $html);
    }

    public function legacy_functions()
    {
        include_once 'includes/functions/get-acf-images.php';
        include_once 'includes/functions/get-acf-titles.php';
        include_once 'includes/functions/convert-the-content.php';

        include_once 'includes/rest-cases.php';
        include_once 'includes/rest-events.php';
        include_once 'includes/rest-inspiration.php';
        include_once 'includes/rest-menus.php';
        include_once 'includes/rest-options.php';
        include_once 'includes/rest-pages.php';
        include_once 'includes/rest-posts.php';
        include_once 'includes/rest-review.php';
    }
}

new StarterSite();
