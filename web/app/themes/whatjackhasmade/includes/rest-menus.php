<?php
/* Register function to run at rest_api_init hook */
add_action('rest_api_init', function () {
    /* Setup siteurl/wp-json/menus/v2/all */
    register_rest_route('menus/v2', '/all', array(
        'methods' => 'GET',
        'callback' => 'all_menus',
        'args' => array(
            'slug' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_string($param);
                },
            ),
        ),
    ));
});

function all_menus($data)
{

    $params = $data->get_params();
    $slug = $params['slug'];

    $menuItems = array();
    $locations = get_nav_menu_locations();

    if (!$slug) {
        foreach ($locations as $current_menu) {
            $array_menu = wp_get_nav_menu_items($current_menu);
            $navObject = wp_get_nav_menu_object($current_menu);

            $menu = array();
            $navChildren = array();

            foreach ($array_menu as $m) {
                if (empty($m->menu_item_parent)) {

                    $slug = str_replace(get_site_url(), "", $m->url);

                    array_push($navChildren, array(
                        'item_id' => $m->ID,
                        'title' => $m->title,
                        'url' => $slug,
                    ));

                    $menu['content'] = $navChildren;
                    $menu['id'] = $navObject->term_id;
                    $menu['slug'] = $navObject->slug;
                }
            }

            array_push($menuItems, $menu);
        }
    } else {
        $array_menu = wp_get_nav_menu_items($slug);
        $navObject = wp_get_nav_menu_object($slug);

        $menu = array();
        $navChildren = array();

        foreach ($array_menu as $m) {
            if (empty($m->menu_item_parent)) {

                $slug = str_replace(get_site_url(), "", $m->url);

                array_push($navChildren, array(
                    'item_id' => $m->ID,
                    'title' => $m->title,
                    'url' => $slug,
                ));

                $menu['content'] = $navChildren;
                $menu['id'] = $navObject->term_id;
                $menu['slug'] = $navObject->slug;
            }
        }

        $menuItems = $menu;
    }

    /* Return array of list items with title and url properties */
    return $menuItems;
}
