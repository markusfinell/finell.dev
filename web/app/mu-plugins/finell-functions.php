<?php
add_action('after_setup_theme', function () {
    register_nav_menu('primary', __('Primary Menu'));
});

add_action('rest_api_init', function () {
    $namespace = 'finell/v1';

    register_rest_route($namespace, '/frontpage', [
        'methods'  => 'GET',
        'callback' => 'finell_get_frontpage',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/pages', [
        'methods'  => 'GET',
        'callback' => 'finell_get_all_pages',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/page/(?P<slug>[a-z0-9_\-]+)', [
        'methods'  => 'GET',
        'callback' => 'finell_get_page_by_slug',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/posts/(?P<slug>[a-z0-9_\-]+)', [
        'methods'  => 'GET',
        'callback' => 'finell_get_post_by_slug',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/posts', [
        'methods'  => 'GET',
        'callback' => 'finell_get_all_posts',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/media', [
        'methods'  => 'GET',
        'callback' => 'finell_get_media',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route($namespace, '/menu/(?P<location>[a-z0-9_]+)/', [
        'methods'  => 'GET',
        'callback' => 'finell_get_menu',
        'permission_callback' => '__return_true'
    ]);
});

function finell_get_block_content($post)
{
    return array_values(
        array_map(
            function ($block) {
                $block['innerHTML'] = trim($block['innerHTML']);
                return $block;
            },
            array_filter(
                parse_blocks($post->post_content),
                function ($block) {
                    return $block['blockName'];
                }
            )
        )
    );
}

function finell_get_page_by_slug($request)
{
    $post = get_page_by_path($request->get_param('slug'), 'object');
    $post->block_content = finell_get_block_content($post);

    return $post;
}

function finell_get_post_by_slug($request)
{
    $post = get_page_by_path($request->get_param('slug'), 'object', ['post']);
    $post->block_content = finell_get_block_content($post);

    return $post;
}

function finell_get_all_pages()
{
    $posts = array_map(
        function ($post) {
            $post->block_content = finell_get_block_content($post);
            return $post;
        },
        get_posts([
            'post_type' => 'page',
            'posts_per_page' => -1
        ])
    );

    return $posts;
}

function finell_get_all_posts()
{
    $posts = array_map(
        function ($post) {
            $post->block_content = finell_get_block_content($post);
            return $post;
        },
        get_posts([
            'post_type' => 'post',
            'posts_per_page' => -1
        ])
    );

    return $posts;
}

function finell_get_frontpage()
{
    $frontpage_id = get_option('page_on_front');

    if (empty($frontpage_id)) {
        // return error
        return 'error';
    }

    $post = get_post($frontpage_id);
    $post->block_content = finell_get_block_content($post);

    return $post;
}

function finell_get_media($object)
{
    $media = get_post($object->get_param('id'));
    if (strpos($media->post_mime_type, 'image') === 0) {
        $media->src = wp_get_attachment_image_src($media->ID, $object->get_param('size') ?: 'full');
        $media->srcset = wp_get_attachment_image_srcset($media->ID, $object->get_param('size') ?: 'full');
        $media->sizes = wp_get_attachment_image_sizes($media->ID, $object->get_param('size') ?: 'full');
    }
    return $media;
}

function finell_get_menu($request)
{
    $location = $request->get_param('location');

    $locations = get_nav_menu_locations();

    $object = wp_get_nav_menu_object($locations[$location]);

    $menu_items = array_map(
        function ($menu_item) {
            $menu_item->url = str_replace(get_home_url(), '', $menu_item->url);
            return $menu_item;
        },
        wp_get_nav_menu_items($object->name)
    );

    return $menu_items;
}
