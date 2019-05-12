<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/case/v2/all */
    register_rest_route('cases/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'rest_cases',
        'args' => array(
            'slug' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));
});

function rest_cases($data)
{
    $args = array(
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'post_type' => 'case',
    );

    $loop = new WP_Query($args);

    if ($loop) {
        $caseItems = array();
        while ($loop->have_posts()): $loop->the_post();
            $content = new stdClass();
            $content->blocks = get_field('blocks');
            $content->gallery = get_field('gallery');
            $content->intro = get_field('intro');
            $content->related = get_field('related');
            $content->siteURL = get_field('site_url');
            $content->testimonials = get_field('testimonials');

            if ((get_field('device_previews'))):
                $content->devices = get_field('devices');
            else:
                $devices = new stdClass();
                $devices->desktop = "";
                $devices->mobile = "";
                $content->devices = $devices;
            endif;

            array_push(
                $caseItems, array(
                    'content' => $content,
                    'date' => get_the_date('c'),
                    'id' => get_the_ID(),
                    'imageXS' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xs'),
                    'imageSM' => get_the_post_thumbnail_url(get_the_ID(), 'featured_sm'),
                    'imageMD' => get_the_post_thumbnail_url(get_the_ID(), 'featured_md'),
                    'imageLG' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                    'imageXL' => get_the_post_thumbnail_url(get_the_ID(), 'featured_xl'),
                    'imageFull' => get_the_post_thumbnail_url(),
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
            'Could not find any case',
            array(
                'status' => 404,
            )
        );
    }

    return $caseItems;
}
