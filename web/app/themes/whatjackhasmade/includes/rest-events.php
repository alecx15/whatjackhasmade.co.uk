<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/event/v2/all */
    register_rest_route('event/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'rest_events',
        'args' => array(
            'slug' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));
});

function rest_events($data)
{
    $args = array(
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'post_type' => 'event',
    );

    $loop = new WP_Query($args);

    if ($loop) {
        $eventItems = array();
        while ($loop->have_posts()): $loop->the_post();
            array_push(
                $eventItems, array(
                    'date' => get_field('date'),
                    'excerpt' => get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true),
                    'id' => get_the_ID(),
                    'imageXS' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xs'),
                    'imageSM' => get_the_post_thumbnail_url(get_the_ID(), 'featured_sm'),
                    'imageMD' => get_the_post_thumbnail_url(get_the_ID(), 'featured_md'),
                    'imageLG' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                    'imageXL' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xl'),
                    'imageFull' => get_the_post_thumbnail_url(),
                    'title' => html_entity_decode(get_the_title()),
                    'venue' => get_field('venue'),
                )
            );
        endwhile;

        wp_reset_postdata();
    } else {
        return new WP_Error(
            'no_menus',
            'Could not find any event',
            array(
                'status' => 404,
            )
        );
    }

    return $eventItems;
}
