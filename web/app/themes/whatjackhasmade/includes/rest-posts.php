<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/posts/v2/all */
    register_rest_route('posts/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'rest_posts',
        'args' => array(
            'slug' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));
});

function rest_posts($data)
{
    $params = $data->get_params();

    $slug = "";

    if (isset($params['slug'])):
        $slug = $params['slug'];
    endif;

    if ($slug != ""):
        $args = array(
            'name' => $slug,
            'numberposts' => 1,
            'post_status' => 'publish',
            'post_type' => 'post',
        );
    else:
        $args = array(
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'post_type' => 'post',
        );
    endif;

    $loop = new WP_Query($args);

    if ($loop) {
        $insightItems = array();
        while ($loop->have_posts()): $loop->the_post();
            $the_content = wpautop(get_the_content());
            array_push(
                $insightItems, array(
                    'content' => $the_content,
                    'date' => get_the_time('c'),
                    'excerpt' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true),
                    'id' => get_the_ID(),
                    'imageLargest' => get_the_post_thumbnail_url(get_the_ID(), 'largest'),
                    'imageDesktop' => get_the_post_thumbnail_url(get_the_ID(), 'desktop'),
                    'imageLaptop' => get_the_post_thumbnail_url(get_the_ID(), 'laptop'),
                    'imageTablet' => get_the_post_thumbnail_url(get_the_ID(), 'tablet'),
                    'imageMobile' => get_the_post_thumbnail_url(get_the_ID(), 'mobile'),
                    'thumbnailTall' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail-tall'),
                    'thumbnailDefault' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail-default'),
                    'thumbnailSmall' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail-small'),
                    'imageFull' => get_the_post_thumbnail_url(),
                    'link' => get_the_permalink(),
                    'seoTitle' => get_post_meta(get_the_ID(), '_yoast_wpseo_title', true),
                    'slug' => get_post_field('post_name'),
                    'title' => html_entity_decode(get_the_title()),
                    'yoast' => array(
                        'description' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true),
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                        'slug' => get_post_field('post_name'),
                        'title' => get_post_meta(get_the_ID(), '_yoast_wpseo_title', true),
                    ),
                )
            );
        endwhile;

        wp_reset_postdata();
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any posts',
            array(
                'status' => 404,
            )
        );
    }

    return $insightItems;
}
