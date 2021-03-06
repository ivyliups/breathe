<?php

add_filter('get_attached_file', 'fifu_replace_attached_file', 10, 2);

function fifu_replace_attached_file($att_url, $att_id) {
    if ($att_url) {
        $url = explode(";", $att_url);
        if (sizeof($url) > 1)
            return fifu_has_internal_image_path($url[1]) ? get_post($att_id)->guid : $url[1];
    }
    return $att_url;
}

add_filter('wp_get_attachment_url', 'fifu_replace_attachment_url', 10, 2);

function fifu_replace_attachment_url($att_url, $att_id) {
    if ($att_url) {
        $url = explode(";", $att_url);
        if (sizeof($url) > 1) {
            $url[1] = fifu_add_url_parameters($url[1], $att_id);
            return fifu_has_internal_image_path($url[1]) ? get_post($att_id)->guid : $url[1];
        } else {
            $post = get_post($att_id);
            if ($post) {
                if ($att_url && strpos($att_url, 'http') === 0 && $post->post_author != "77777")
                    return $att_url;

                if (!fifu_reject_guid())
                    return get_post($att_id)->guid;
            }
        }
    }
    return $att_url;
}

add_filter('posts_where', 'fifu_query_attachments');

function fifu_query_attachments($where) {
    if (isset($_POST['action']) && ($_POST['action'] == 'query-attachments') && fifu_is_off('fifu_media_library')) {
        global $wpdb;
        $where .= ' AND ' . $wpdb->prefix . 'posts.post_author <> 77777 ';
    }
    return $where;
}

add_filter('posts_where', function ($where, \WP_Query $q) {
    if (is_admin() && $q->is_main_query() && fifu_is_off('fifu_media_library')) {
        global $wpdb;
        $where .= ' AND ' . $wpdb->prefix . 'posts.post_author <> 77777 ';
    }
    return $where;
}, 10, 2);

add_filter('wp_get_attachment_image_src', 'fifu_replace_attachment_image_src', 10, 3);

function fifu_replace_attachment_image_src($image, $att_id, $size) {
    if (!$image)
        return $image;

    if (strpos($image[0], ';http') !== false)
        $image[0] = 'http' . explode(";http", $image[0])[1];

    if (!$att_id)
        return $image;

    $post = get_post($att_id);

    if (fifu_has_internal_image_path($image[0]) && $post->post_author != "77777")
        return $image;

    if (fifu_should_hide())
        return null;
    $image_size = fifu_get_image_size($size);
    if (fifu_is_on('fifu_original')) {
        return array(
            fifu_has_internal_image_path($image[0]) ? get_post($att_id)->guid : $image[0],
            null,
            null,
            null,
        );
    }
    $dimension = $post ? get_post_meta($post->ID, 'fifu_image_dimension') : null;
    $arrFIFU = fifu_get_width_height($dimension);

    $width = $arrFIFU['width'];
    if (isset($image_size['width'])) {
        $is = $image_size['width'];
        if (!$width || (!$dimension && $is < $width))
            $width = $is;
    }

    $height = $arrFIFU['height'];
    if (isset($image_size['height'])) {
        $is = $image_size['height'];
        if (!$height || (!$dimension && $is < $height))
            $height = $is;
    }

    return array(
        fifu_has_internal_image_path($image[0]) ? get_post($att_id)->guid : $image[0],
        $width,
        $height,
        isset($image_size['crop']) ? $image_size['crop'] : '',
    );
}

function fifu_get_internal_image_path() {
    return explode("//", get_home_url())[1] . "/wp-content/uploads/";
}

function fifu_get_internal_image_path2() {
    return get_bloginfo() . ".files.wordpress.com";
}

function fifu_get_internal_image_path3() {
    return explode('.', explode("//", get_home_url())[1])[0] . ".files.wordpress.com";
}

// for WPML Multilingual CMS
function fifu_get_internal_image_path4() {
    return explode("/", explode("//", get_home_url())[1])[0] . "/wp-content/uploads/";
}

function fifu_has_internal_image_path($url) {
    return strpos($url, fifu_get_internal_image_path()) !== false || strpos($url, fifu_get_internal_image_path2()) !== false || strpos($url, fifu_get_internal_image_path3()) !== false || strpos($url, fifu_get_internal_image_path4()) !== false;
}

add_action('template_redirect', 'fifu_action', 10);

function fifu_action() {
    ob_start("fifu_callback");
}

function fifu_callback($buffer) {
    if (empty($buffer))
        return;

    /* img */

    $srcType = "src";
    $imgList = array();
    preg_match_all('/<img[^>]*>/', $buffer, $imgList);

    foreach ($imgList[0] as $imgItem) {
        preg_match('/(' . $srcType . ')([^\'\"]*[\'\"]){2}/', $imgItem, $src);
        if (!$src)
            continue;
        $del = substr($src[0], - 1);
        $url = fifu_normalize(explode($del, $src[0])[1]);
        $post_id = null;

        // get parameters
        if (isset($_POST[$url]))
            $data = $_POST[$url];
        else
            continue;

        if (strpos($imgItem, 'fifu-replaced') !== false)
            continue;

        $post_id = $data['post_id'];
        $featured = $data['featured'];

        if ($featured) {
            // add featured
            $newImgItem = str_replace('<img ', '<img fifu-featured="1" ', $imgItem);

            $buffer = str_replace($imgItem, fifu_replace($newImgItem, $post_id, null, null), $buffer);
        }
    }

    /* background-image */

    $imgList = array();
    preg_match_all('/<[^>]*background-image[^>]*>/', $buffer, $imgList);
    foreach ($imgList[0] as $imgItem) {
        $mainDelimiter = substr(explode('style=', $imgItem)[1], 0, 1);
        $subDelimiter = substr(explode('url(', $imgItem)[1], 0, 1);
        if (in_array($subDelimiter, array('"', "'", ' ')))
            $url = preg_split('/[\'\" ]{1}\)/', preg_split('/url\([\'\" ]{1}/', $imgItem, -1)[1], -1)[0];
        else
            $url = preg_split('/\)/', preg_split('/url\(/', $imgItem, -1)[1], -1)[0];

        $newImgItem = $imgItem;

        $url = fifu_normalize($url);
        if (isset($_POST[$url])) {
            $data = $_POST[$url];

            if (strpos($imgItem, 'fifu-replaced') !== false)
                continue;
        }

        if (fifu_is_on('fifu_lazy')) {
            // lazy load for background-image
            $class = 'lazyload ';

            // add class
            $newImgItem = str_replace('class=' . $mainDelimiter, 'class=' . $mainDelimiter . $class, $imgItem);

            // add status
            $newImgItem = str_replace('<img ', '<img fifu-replaced="1" ', $newImgItem);

            $attr = 'data-bg=' . $mainDelimiter . $url . $mainDelimiter;
            $newImgItem = str_replace('>', ' ' . $attr . '>', $newImgItem);

            // remove background-image
            $pattern = '/background-image.*url\(' . $subDelimiter . '.*' . $subDelimiter . '\)/';
            $newImgItem = preg_replace($pattern, '', $newImgItem);
        }

        if ($newImgItem != $imgItem)
            $buffer = str_replace($imgItem, $newImgItem, $buffer);
    }

    return $buffer;
}

add_filter('wp_get_attachment_metadata', 'fifu_filter_wp_get_attachment_metadata', 10, 2);

function fifu_filter_wp_get_attachment_metadata($data, $post_id) {
    if (!$data || !is_array($data)) {
        $dimension = get_post_meta($post_id, 'fifu_image_dimension');
        return fifu_get_width_height($dimension);
    }
    return $data;
}

function fifu_get_width_height($dimension) {
    if ($dimension && fifu_is_on('fifu_save_dimensions')) {
        $dimension = $dimension[0];
        $width = explode(';', $dimension)[0];
        $height = explode(';', $dimension)[1];
    } else {
        $dimension = null;
        $width = fifu_maximum('width');
        $height = fifu_maximum('height');

        // a value is required, otherwise the zoom doesn't work
        if (!$width && is_singular('product'))
            $width = 1000;
    }
    return array('width' => $width, 'height' => $height);
}

// for themes that dont call post_thumbnail_html

function fifu_add_url_parameters($url, $att_id) {
    $post_id = get_post($att_id)->post_parent;

    if (!$post_id)
        return $url;

    $post_thumbnail_id = get_post_thumbnail_id($post_id);
    $post_thumbnail_id = $post_thumbnail_id ? $post_thumbnail_id : get_term_meta($post_id, 'thumbnail_id', true);
    $featured = $post_thumbnail_id == $att_id ? 1 : 0;

    if (!$featured)
        return $url;

    // avoid duplicated call
    if (isset($_POST[$url]))
        return $url;

    $parameters = array();
    $parameters['att_id'] = $att_id;
    $parameters['post_id'] = $post_id;
    $parameters['featured'] = $featured;

    $_POST[$url] = $parameters;
    return $url;
}

// plugin: accelerated-mobile-pages

function fifu_amp_url($url, $width, $height) {
    $size = get_post_meta(get_the_ID(), 'fifu_image_dimension');
    if (!empty($size)) {
        $size = explode(';', $size[0]);
        $width = $size[0];
        $height = $size[1];
    }
    return array(0 => $url, 1 => $width, 2 => $height);
}

function fifu_reject_guid() {
    return fifu_is_mpd_active() || fifu_is_classified_listing_active();
}

// plugin: multisite-post-duplicator

function fifu_is_mpd_active() {
    return is_plugin_active('multisite-post-duplicator/mpd.php');
}

// plugin: classified-listing

function fifu_is_classified_listing_active() {
    return is_plugin_active('classified-listing/classified-listing.php') || is_plugin_active('classified-listing-pro/classified-listing-pro.php');
}

