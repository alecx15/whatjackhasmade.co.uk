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

function fallbackString($val)
{
    if (!$val) {
        return "";
    }

    return $val;
}

function relatedPosts($posts)
{
    $postsObjects = [];
    $postObject = new stdClass();

    if (is_array($posts) || is_object($posts)):
        foreach ($posts as $value) {
            $id = $value->ID;

            if (get_post_status($id) === 'publish'):
                $postObject = new stdClass();
                $postObject->id = $id;
                $postObject->date = fallbackString(get_the_time('c'));
                $postObject->excerpt = fallbackString(get_post_meta($id, '_yoast_wpseo_metadesc', true));
                $postObject->imageXS = fallbackString(get_the_post_thumbnail_url($id, 'featured_xs'));
                $postObject->imageSM = fallbackString(get_the_post_thumbnail_url($id, 'featured_sm'));
                $postObject->imageMD = fallbackString(get_the_post_thumbnail_url($id, 'featured_md'));
                $postObject->imageLG = fallbackString(get_the_post_thumbnail_url($id, 'featured_lg'));
                $postObject->imageXL = fallbackString(get_the_post_thumbnail_url($id, 'featured_xl'));
                $postObject->imageFull = fallbackString(get_the_post_thumbnail_url());
                $postObject->link = fallbackString(get_the_permalink());
                $postObject->slug = fallbackString(get_post_field('post_name', $id));
                $postObject->title = fallbackString(get_the_title($id));

                $postsObjects[] = $postObject;
            endif;
        }
    else :
        $postsObjects[] = $postObject;
    endif;

    return $postsObjects;
}

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
            $id = get_the_ID();
            array_push(
                $insightItems, array(
                    'content' => $the_content,
                    'date' => get_the_time('c'),
                    'excerpt' => get_post_meta($id, '_yoast_wpseo_metadesc', true),
                    'id' => $id,
                    'imageXS' => fallbackString(get_the_post_thumbnail_url($id, 'featured_xs')),
                    'imageSM' => fallbackString(get_the_post_thumbnail_url($id, 'featured_sm')),
                    'imageMD' => fallbackString(get_the_post_thumbnail_url($id, 'featured_md')),
                    'imageLG' => fallbackString(get_the_post_thumbnail_url($id, 'featured_lg')),
                    'imageXL' => fallbackString(get_the_post_thumbnail_url($id, 'featured_xl')),
                    'imageFull' => fallbackString(get_the_post_thumbnail_url($id)),
                    'link' => fallbackString(get_the_permalink()),
                    'related' => relatedPosts(get_field('related_posts')),
                    'seoTitle' => get_post_meta($id, '_yoast_wpseo_title', true),
                    'slug' => fallbackString(get_post_field('post_name')),
                    'title' => html_entity_decode(get_the_title()),
                    'yoast' => array(
                        'description' => get_post_meta($id, '_yoast_wpseo_metadesc', true),
                        'image' => get_the_post_thumbnail_url($id, 'featured_lg'),
                        'slug' => get_post_field('post_name'),
                        'title' => get_post_meta($id, '_yoast_wpseo_title', true),
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
