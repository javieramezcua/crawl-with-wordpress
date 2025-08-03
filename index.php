<?php
/*
Plugin Name: Crawl a Site
Description: allows you to crawl a site and extract elements using a selector.
Version: 1.0
Author: Jamezcua
*/

// create admin menu page
add_action('admin_menu', function() {
    add_menu_page('Crawl a Site', 'Crawl a Site', 'manage_options', 'crawl-a-site', 'crawl_a_site_admin_page');
});

// Options page
function crawl_a_site_admin_page() {
    ?>
    <div class="wrap">
        <h1>Crawl a Site</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('crawl_a_site_options');
            do_settings_sections('crawl_a_site');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register options
add_action('admin_init', function() {
    register_setting('crawl_a_site_options', 'crawl_a_site_url');
    register_setting('crawl_a_site_options', 'crawl_a_site_selector');

    add_settings_section('crawl_a_site_section', 'Configuración', null, 'crawl_a_site');

    add_settings_field('crawl_a_site_url', 'URL a rastrear', function() {
        $value = esc_attr(get_option('crawl_a_site_url', ''));
        echo "<input type='text' name='crawl_a_site_url' value='$value' style='width:400px;' />";
    }, 'crawl_a_site', 'crawl_a_site_section');

    add_settings_field('crawl_a_site_selector', 'Selector de elemento', function() {
        $value = esc_attr(get_option('crawl_a_site_selector', ''));
        echo "<input type='text' name='crawl_a_site_selector' value='$value' style='width:400px;' />";
    }, 'crawl_a_site', 'crawl_a_site_section');
});

// Cron each 12 hours
register_activation_hook(__FILE__, function() {
    if (!wp_next_scheduled('crawl_a_site_cron_hook')) {
        wp_schedule_event(time(), 'twicedaily', 'crawl_a_site_cron_hook');
    }
});
register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('crawl_a_site_cron_hook');
});

// cron job function
add_action('crawl_a_site_cron_hook', function() {
    $output = null;
    $return_var = null;
    $bash_path = plugin_dir_path(__FILE__) . 'crawler.sh';

    // Get the URL and selector from options
    $url = escapeshellarg(get_option('crawl_a_site_url', ''));
    $selector = escapeshellarg(get_option('crawl_a_site_selector', ''));

    if (file_exists($bash_path)) {
        // Pass the parameters to the bash script
        exec("bash $bash_path $url $selector", $output, $return_var);
    }
});