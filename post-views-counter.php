<?php
/*
Plugin Name: TRT Post Views
Description: WordPress Post views counter with custom query
Version: 1.2
Author: TRT Post Views
Author URI: https://www.linkedin.com/in/tahsinur-tamim-95707b170/
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Function to include css
function trt_pvc_enqueue_styles() {
    wp_enqueue_style('trt-pvc-styles', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
    $custom_css = get_option('trt_pvc_custom_css', '');
    wp_add_inline_style('trt-pvc-styles', $custom_css);
}
add_action('wp_enqueue_scripts', 'trt_pvc_enqueue_styles');

function trt_pvc_enqueue_scripts() {
    wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'trt_pvc_enqueue_scripts');


// Function to increment post views
function trt_pvc_increment_post_views($post_id) {
    $track_ip = get_option('trt_pvc_track_ip', false);

    // Sanitize IP address
    $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);

    // Initialize tracked_ips as an array if it doesn't exist
    $tracked_ips = get_post_meta($post_id, 'trt_pvc_tracked_ips', true);
    $tracked_ips = is_array($tracked_ips) ? $tracked_ips : array();

    // Check if IP tracking is enabled
    if ($track_ip && $ip) {
        // Check if the IP has already been tracked
        if (!in_array($ip, $tracked_ips)) {
            // Increment views and track IP
            $views = get_post_meta($post_id, 'trt_pvc_post_views', true);
            $views = empty($views) ? 1 : $views + 1;

            // Save views as a post meta
            update_post_meta($post_id, 'trt_pvc_post_views', $views);

            $tracked_ips[] = $ip;
            update_post_meta($post_id, 'trt_pvc_tracked_ips', $tracked_ips);
        }
    } else {
        // IP tracking is disabled or invalid IP, simply increment views
        $views = get_post_meta($post_id, 'trt_pvc_post_views', true);
        $views = empty($views) ? 1 : $views + 1;

        // Save views as a post meta
        update_post_meta($post_id, 'trt_pvc_post_views', $views);
    }
}

// Function to display post views
function trt_pvc_display_post_views($post_id) {
    $views = get_post_meta($post_id, 'trt_pvc_post_views', true);
    echo '<span class="trt-pvc-post-views-count">' . esc_html($views) . ' Views</span>';
}

// Function to display total views in admin column list
function trt_pvc_display_admin_column($column, $post_id) {
    if ($column == 'trt_pvc_post_views') {
        $views = get_post_meta($post_id, 'trt_pvc_post_views', true);
        echo esc_html($views);
    }
}

// Hook to add the views column to the admin list
function trt_pvc_add_admin_column($columns) {
    $columns['trt_pvc_post_views'] = 'Post Views';
    return $columns;
}
add_filter('manage_posts_columns', 'trt_pvc_add_admin_column');
add_action('manage_posts_custom_column', 'trt_pvc_display_admin_column', 10, 2);

// Hook to increment views on post load
function trt_pvc_track_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        trt_pvc_increment_post_views($post_id);
    }
}
add_action('wp_head', 'trt_pvc_track_post_views');

// Shortcode to display total post views
function trt_pvc_total_post_views_shortcode($atts) {
    global $post;

    $atts = shortcode_atts(array(
        'post_id' => $post->ID,
    ), $atts, 'trt_pvc_post_views');

    ob_start(); ?>
    <span id="trt-pvc-post-views-<?php echo esc_attr($atts['post_id']); ?>" class="trt-pvc-post-views-count">
        <?php trt_pvc_display_post_views($atts['post_id']); ?>
    </span>
    <?php return ob_get_clean();
}
add_shortcode('trt_pvc_post_views', 'trt_pvc_total_post_views_shortcode');

function trt_pvc_custom_query_shortcode() {
    $grid_columns = get_option('trt_pvc_archive_columns', 3); // Retrieve grid columns setting
    $posts_per_page = get_option('trt_pvc_posts_per_page', 10); // Retrieve posts per page setting

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page, // Use posts per page setting
        'meta_key'       => 'trt_pvc_post_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'paged'          => $paged, // Set the current page
    );

    $custom_query = new WP_Query($args);

    if ($custom_query->have_posts()) :
        $output = '<style>.trt-pvc-post-grid { display: grid; grid-template-columns: repeat(' . $grid_columns . ', 1fr); grid-gap: 20px; }</style>'; // Dynamic CSS
        $output .= '<div class="trt-pvc-post-grid" id="trt-pvc-post-grid">';
        while ($custom_query->have_posts()) : $custom_query->the_post();
            $output .= '<div class="trt-pvc-post-item">'; // Removed inline width style
            if (has_post_thumbnail()) {
                $output .= '<div class="trt-pvc-featured-image">' . get_the_post_thumbnail(null, 'thumbnail') . '</div>';
            }
            $output .= '<div class="trt-pvc-post-details">';
            $output .= '<h2 class="trt-pvc-post-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
            $output .= '<span class="trt-pvc-post-views-count">Views: ' . get_post_meta(get_the_ID(), 'trt_pvc_post_views', true) . '</span>';
            $output .= '</div>';
            $output .= '</div>';
        endwhile;
        $output .= '</div>';

        // Pagination
        $output .= '<div class="trt-pvc-pagination" id="trt-pvc-pagination">' . paginate_links( array(
            'total'     => $custom_query->max_num_pages,
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'format'    => '?paged=%#%', // Adjust base if needed
            'prev_text' => __('&laquo; Previous'),
            'next_text' => __('Next &raquo;'),
            'type'      => 'list', // Output as an unordered list
            'end_size'  => 1,
            'mid_size'  => 1,
        ) ) . '</div>';

        // Add AJAX script
        $output .= '<script>
            jQuery(function($){
                $(document).on("click", ".trt-pvc-pagination a", function(e) {
                    e.preventDefault();
                    var link = $(this).attr("href");
                    $("#trt-pvc-post-grid").html("<div class=\'trt-pvc-loading\'>Loading...</div>");
                    $.get(link, function(data) {
                        var result = $(data).find("#trt-pvc-post-grid").html();
                        var pagination = $(data).find(".trt-pvc-pagination").html();
                        $("#trt-pvc-post-grid").html(result);
                        $("#trt-pvc-pagination").html(pagination);
                        $("html, body").animate({ scrollTop: $("#trt-pvc-post-grid").offset().top }, 1000); // Scroll to top of grid
                    });
                });
            });
        </script>';

        wp_reset_postdata(); // Reset post data to the main query
    else :
        $output = 'No posts found';
    endif;

    return $output;
}
add_shortcode('trt_pvc_custom_query', 'trt_pvc_custom_query_shortcode');


// Admin settings menu
function trt_pvc_admin_menu() {
    add_options_page('TRT Post Views Settings', 'TRT Post Views', 'manage_options', 'trt-pvc-settings', 'trt_pvc_settings_page');
}
add_action('admin_menu', 'trt_pvc_admin_menu');

// Register plugin settings
function trt_pvc_register_settings() {
    register_setting('trt_pvc_settings_group', 'trt_pvc_show_column');
    register_setting('trt_pvc_settings_group', 'trt_pvc_track_ip');
    register_setting('trt_pvc_settings_group', 'trt_pvc_custom_post_type');
    register_setting('trt_pvc_settings_group', 'trt_pvc_sort_by_views');
    register_setting('trt_pvc_settings_group', 'trt_pvc_custom_css');
    register_setting('trt_pvc_settings_group', 'trt_pvc_archive_columns');
    register_setting('trt_pvc_settings_group', 'trt_pvc_posts_per_page'); 
}

add_action('admin_init', 'trt_pvc_register_settings');


// Settings page content
function trt_pvc_settings_page() {
    ?>
    <div class="wrap">
        <h1>TRT Post Views Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('trt_pvc_settings_group'); ?>
            <?php do_settings_sections('trt_pvc_settings_group'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">Show Post Views Column</th>
                    <td><input type="checkbox" name="trt_pvc_show_column" value="1" <?php checked(get_option('trt_pvc_show_column', false)); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Views Filter on IP</th>
                    <td><input type="checkbox" name="trt_pvc_track_ip" value="1" <?php checked(get_option('trt_pvc_track_ip', false)); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Select Custom Post Type</th>
                    <td>
                        <?php
                        $post_types = get_post_types(array('public' => true), 'names');
                        $selected_post_type = get_option('trt_pvc_custom_post_type', 'post');
                        ?>
                        <select name="trt_pvc_custom_post_type">
                            <?php foreach ($post_types as $post_type) : ?>
                                <option value="<?php echo esc_attr($post_type); ?>" <?php selected($selected_post_type, $post_type); ?>><?php echo esc_html($post_type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Sort Posts by Views (Custom Post Type)</th>
                    <td><input type="checkbox" name="trt_pvc_sort_by_views" value="1" <?php checked(get_option('trt_pvc_sort_by_views', false)); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">Number of Columns in Archive Grid</th>
                    <td>
                        <input type="number" name="trt_pvc_archive_columns" min="1" step="1" value="<?php echo esc_attr(get_option('trt_pvc_archive_columns', 3)); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">Posts Per Page</th>
                    <td>
                        <input type="number" name="trt_pvc_posts_per_page" min="1" step="1" value="<?php echo esc_attr(get_option('trt_pvc_posts_per_page', 10)); ?>" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">Custom CSS</th>
                    <td>
                        <textarea name="trt_pvc_custom_css" rows="5" cols="50"><?php echo esc_textarea(get_option('trt_pvc_custom_css', '')); ?></textarea>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
        <div class="wrap">
      <div class="trt-pvc-usage-instructions">
            <h2>Plugin Usage Instructions</h2>
            <p>To display post views on a post/page, use the following shortcode:</p>
            <pre>[trt_pvc_post_views]</pre>
            
            <p>To display a custom query of posts ordered by views in a 4-column grid, use the following shortcode:</p>
            <pre>[trt_pvc_custom_query]</pre>
        </div>
    </div>
    </div>
    <?php
}