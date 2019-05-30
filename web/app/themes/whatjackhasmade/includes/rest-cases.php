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

            if ((get_field('related'))):
                $relatedObjects = [];
                $related = get_field('related');

                if (is_array($related) || is_object($related)):
                    foreach ($related as $value) {
                        $id = intval($value->ID);

                        if (get_post_status($id) === 'publish'):
                            $caseObject = new stdClass();
                            $caseObject->id = $id;
                            $caseObject->date = get_the_date('c', $id) ? get_the_date('c', $id) : "";
                            $caseObject->imageXS = get_the_post_thumbnail_url($id, 'featured_xs') ? get_the_post_thumbnail_url($id, 'featured_xs') : "";
                            $caseObject->imageSM = get_the_post_thumbnail_url($id, 'featured_sm') ? get_the_post_thumbnail_url($id, 'featured_sm') : "";
                            $caseObject->imageMD = get_the_post_thumbnail_url($id, 'featured_md') ? get_the_post_thumbnail_url($id, 'featured_md') : "";
                            $caseObject->imageLG = get_the_post_thumbnail_url($id, 'featured_lg') ? get_the_post_thumbnail_url($id, 'featured_lg') : "";
                            $caseObject->imageXL = get_the_post_thumbnail_url($id, 'featured_xl') ? get_the_post_thumbnail_url($id, 'featured_xl') : "";
                            $caseObject->imageFull = get_the_post_thumbnail_url($id) ? get_the_post_thumbnail_url($id) : "";

                            $caseObject->slug = get_post_field('post_name', $id) ? get_post_field('post_name', $id) : "";
                            $caseObject->title = html_entity_decode(get_the_title($id)) ? html_entity_decode(get_the_title($id)) : "";

                            $yoastArray = [];

                            $post = get_post($id, ARRAY_A);
                            $yoast_title = get_post_meta($id, '_yoast_wpseo_title', true);
                            $yoast_desc = get_post_meta($id, '_yoast_wpseo_metadesc', true);

                            $metatitle_val = wpseo_replace_vars($yoast_title, $post);
                            $metatitle_val = apply_filters('wpseo_title', $metatitle_val);
                            $metatitle_val = html_entity_decode($metatitle_val);

                            $metadesc_val = wpseo_replace_vars($yoast_desc, $post);
                            $metadesc_val = apply_filters('wpseo_metadesc', $metadesc_val);

                            $yoastArray['description'] = $metadesc_val;
                            $yoastArray['image'] = get_the_post_thumbnail_url($id, 'featured_lg') ? get_the_post_thumbnail_url($id, 'featured_lg') : "";
                            $yoastArray['slug'] = get_post_field('post_name', $id) ? get_post_field('post_name', $id) : "";
                            $yoastArray['title'] = $metatitle_val;

                            $caseObject->yoast = $yoastArray;


                            $relatedObjects[] = $caseObject;
                        endif;
                    }
                endif;
            else:
                $caseObject = new stdClass();
                $caseObject->id = "";
                $relatedObjects[] = $caseObject;
            endif;

            $content->related = $relatedObjects;

            $id = get_the_ID();
            $post = get_post($id, ARRAY_A);
            $yoast_title = get_post_meta($id, '_yoast_wpseo_title', true);
            $yoast_desc = get_post_meta($id, '_yoast_wpseo_metadesc', true);

            $metatitle_val = wpseo_replace_vars($yoast_title, $post);
            $metatitle_val = apply_filters('wpseo_title', $metatitle_val);
            $metatitle_val = html_entity_decode($metatitle_val);

            $metadesc_val = wpseo_replace_vars($yoast_desc, $post);
            $metadesc_val = apply_filters('wpseo_metadesc', $metadesc_val);

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
                        'description' => $metadesc_val,
                        'image' => get_the_post_thumbnail_url(get_the_ID(), 'featured_lg'),
                        'slug' => get_post_field('post_name'),
                        'title' => $metatitle_val,
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
