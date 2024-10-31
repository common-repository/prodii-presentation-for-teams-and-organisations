<?php
/*
Plugin Name: Prodii Presentation - for Teams and Organisations
Plugin URI: https://prodii.com
Description:  Mirroring data from your Prodii account to your homepage
Version: 0.1.0
Author: TeamProdii
Author URI: https://prodii.com
Text Domain: prodii-presentation
Domain Path: /prodii-presentation
*/

require_once 'functions.php';

define('PRODII_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PRODII_PLUGIN_ENDPOINT', 'https://prodii.com/common/api/apihandler.php');

add_action('plugins_loaded', function () {
    if (class_exists('Prodii')) {
        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error is-dismissible"><p>Prodii Presentation cannot load - Class "<b>Prodii</b>" is already defined</p>
                <p>Please check your theme and/or plugins</p></div>
            <?php
        });
    } else {
        require_once 'Prodii.php';
        new Prodii();
    }
});

add_action('prodii_load_templates', function () {
    register_prodii_template(__DIR__ . '/prodii-templates/copenhagen/');
});
