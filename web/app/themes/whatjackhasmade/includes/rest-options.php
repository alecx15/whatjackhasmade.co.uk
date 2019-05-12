<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/options/v2/all */
    register_rest_route('options/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'option_menu',
        'args' => array(
            'id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                },
            ),
        ),
    ));
});

function option_menu($data)
{
    if (function_exists('acf_add_options_page')) {
        $optionFields = get_fields('options');
    } else {
        return new WP_Error(
            'no_options',
            'Could not find any options',
            array(
                'status' => 404,
            )
        );
    }

    /* Return array of list items with title and url properties */
    return $optionFields;
}
