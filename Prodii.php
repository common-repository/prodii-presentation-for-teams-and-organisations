<?php

class Prodii {
    private static $_classes                   = array(
        'ProdiiBase',
        'ProdiiList',
        'ProdiiCompany',
        'ProdiiTeam',
        'ProdiiProfile',
        'ProdiiTemplate',
        'ProdiiCache',
        'ProdiiCurl',
        'ProdiiShortcode',
        'ProdiiAjax',
    );
    private static $_settings                  = array(
        'prodii_access_token'     => array('type' => 'string', 'default' => '', 'label' => 'Your Account Key (Access Token)', 'input' => 'text'),
        'prodii_default_template' => array(
            'type'    => 'string',
            'default' => 'copenhagen',
            'label'   => 'Default Template',
            'input'   => 'select',
            'options' => 'get_templates'
        ),
    );
    private static $_default_privacy_overrride = false;
    const COMPANY = 'company';
    const TEAM    = 'team';
    const PROFILE = 'profile';

    public static function get_default_privacy_override() {
        return self::$_default_privacy_overrride;
    }

    public function __construct() {
        $this->init();
    }

    public static function get_name() {
        return 'Prodii Presentation';
    }

    public function setup() {
    }

    public function init() {
        $this->load_deps();
        $this->load_settings();
        $this->prep_cache_dir();
        $this->load_settings_pages();

        $this->load_templates();

        $this->action_handler();
    }

    public function load_templates() {
        add_action(
            'after_setup_theme', function () {
            do_action('prodii_load_templates');
        }
        );
    }

    // region Admin Pages

    private function load_settings_pages() {
        add_action(
            'admin_enqueue_scripts', function () {
            $page = isset($_GET['page']) ? $_GET['page'] : '';
            if (strpos($page, 'prodii') !== false) {
                wp_register_style('prodii_wp_admin_styles', self::get_plugin_url() . 'assets/dist/style/admin.css', false, '1.0.0');
                wp_enqueue_style('prodii_wp_admin_styles');
                wp_register_script('prodii_wp_admin_scripts', self::get_plugin_url() . 'assets/dist/js/admin.js', false, '1.0.0');
                wp_enqueue_script('prodii_wp_admin_scripts');

                wp_register_style('prodii_highlight_css', self::get_plugin_url() . 'assets/dist/style/highlight.css', false, '1.0.0');
                wp_enqueue_style('prodii_highlight_css');
                wp_register_script('prodii_highlight_js', self::get_plugin_url() . 'assets/dist/js/highlight.js', false, '1.0.0');
                wp_enqueue_script('prodii_highlight_js');
            }
        }
        );

        add_action(
            'admin_menu', function () {
            if (isset($_GET['settings-updated']) && strpos($_GET['page'], 'prodii') !== false) {
                self::show_admin_success('Settings updated!');
            }

            $pages = array();

            foreach (glob(prodii_path_convert(self::get_plugin_dir() . 'pages/*.php')) as $page) {
                $headers = array(
                    'main_title' => 'Main Title',
                    'title'      => 'Page Title',
                    'menu_title' => 'Menu Title',
                    'parent'     => 'Parent',
                    'capability' => 'Capability',
                    'icon'       => 'Icon',
                    'position'   => 'Position',
                    'type'       => 'Type',
                );
                $type    = str_replace(['page-', '.php'], '', basename($page));
                $data    = self::get_file_data_header($page, $headers);

                $pages[] = array('type' => $type, 'data' => $data);
            }

            usort(
                $pages, function ($e) {
                if (in_array('main', $e['data']['type'])) {

                    return -1;
                }

                return 1;
            }
            );

            foreach ($pages as $page) {
                $data = $page['data'];
                $type = $page['type'];
                if (in_array('main', $data['type'])) {
                    add_menu_page(
                        $data['title'], $data['menu_title'][array_search('main', $data['type'])], $data['capability'], "prodii-$type",
                        array(&$this, 'show_settings_page'),
                        plugins_url($data['icon'], __FILE__), $data['position']
                    );
                }
                if (in_array('sub', $data['type'])) {
                    add_submenu_page(
                        "prodii-$data[parent]", $data['title'], $data['menu_title'][array_search('sub', $data['type'])], $data['capability'],
                        "prodii-$type",
                        array(&$this, 'show_settings_page')
                    );
                }
            }
        }
        );
    }

    public static function get_file_data_header($file, $headers = array()) {
        $default_headers = array();

        foreach ($headers as $key => $header) {
            $default_headers[$key] = $header;
        }

        $data = get_file_data($file, $default_headers, 'plugin');

        $data['type'] = isset($data['type']) ? preg_split('/\||\,|\./', $data['type']) : null;

        $data['menu_title'] = isset($data['menu_title']) ? preg_split('/\||\,|\./', $data['menu_title']) : null;

        return $data;
    }

    public function show_settings_page() {
        $type  = str_replace('prodii-', '', $_GET['page']);
        $title = '';

        ob_start();
        require_once prodii_path_convert(self::get_plugin_dir() . "pages/page-$type.php");

        $pages = array();
        foreach (glob(prodii_path_convert(self::get_plugin_dir() . 'pages/*.php')) as $page) {
            $headers = array(
                'main_title' => 'Main Title',
                'title'      => 'Page Title',
                'menu_title' => 'Menu Title',
                'parent'     => 'Parent',
                'capability' => 'Capability',
                'icon'       => 'Icon',
                'position'   => 'Position',
                'type'       => 'Type',
            );
            $type    = str_replace(['page-', '.php'], '', basename($page));
            $data    = self::get_file_data_header($page, $headers);

            $pages[] = array('type' => $type, 'data' => $data, 'page' => $page);
        }

        usort(
            $pages, function ($e) {
            if (in_array('main', $e['data']['type'])) {

                return -1;
            }

            return 1;
        }
        );
        $buttons = array();
        foreach ($pages as $page) {
            $data      = $page['data'];
            $slug      = str_replace('page-', 'prodii-', pathinfo($page['page'], PATHINFO_FILENAME));
            $buttons[] = $slug !== $_GET['page'] ? "<a href='admin.php?page=$slug' class='button button-secondary'>$data[title]</a>" :
                "<a href='javascript:void(0)' class='button button-primary' disabled>$data[title]</a>";
            $title     = $slug === $_GET['page'] ? $data['title'] : $title;
        }
        $buttons = implode('', $buttons);

        $plugin_name = Prodii::get_name();
        echo sprintf(
            '<div class="wrap"><h1 class="wp-heading">%s - %s</h1><div class="button-group">%s</div>%s</div>', $plugin_name, $title, $buttons,
            ob_get_clean()
        );
    }

    // endregion

    // region Cache

    private function prep_cache_dir() {
        ProdiiCache::prep_cache_dir();
    }

    public static function flush_cache() {
        $result = ProdiiCache::flush_cache();
        $result ? self::show_admin_success("Prodii Cache succesfully cleared!") :
            self::show_admin_warning("Error clearing Prodii Cache - Please check file permissions for " . self::get_cache_dir());
    }

    // endregion

    // region Load

    private function load_settings() {
        add_action(
            'admin_init', function () {
            foreach (self::$_settings as $key => $args) {
                register_setting('prodii-presentation', $key, $args);
            }
        }
        );
    }

    private function load_deps() {
        foreach (self::$_classes as $class) {
            $class_path = prodii_path_convert(PRODII_PLUGIN_PATH . "classes/$class.php");
            if (class_exists($class)) {
                self::show_admin_error("Prodii Presentation failed to load due the class '$class' already existing");

                return;
            } else {
                if (!file_exists($class_path)) {
                    self::show_admin_error("Prodii Presentation failed to load due the class '$class' wasn't able to load");

                    return;
                }
            }

            require_once $class_path;
        }
    }

    // endregion

    // region Admin Errors

    /**
     * Shows an admin notice on the dashboard
     *
     * @param string $message
     * @param string $type
     */
    public static function show_admin_notice($message, $type = 'success') {
        add_action(
            'admin_notices', function () use ($message, $type) {
            ?>
            <div class="notice notice-<?php echo $type ?> is-dismissible">
                <p><?php _e($message, 'prodii-presentation') ?></p></div>
            <?php
        }
        );
    }

    /**
     * @param string $message
     *
     * @see \Prodii::show_admin_notice()
     */
    public static function show_admin_success($message) {
        self::show_admin_notice($message, 'success');
    }

    /**
     * @param string $message
     *
     * @see \Prodii::show_admin_notice()
     */
    public static function show_admin_warning($message) {
        self::show_admin_notice($message, 'warning');
    }

    /**
     * @param string $message
     *
     * @see \Prodii::show_admin_notice()
     */
    public static function show_admin_info($message) {
        self::show_admin_notice($message, 'info');
    }

    /**
     * @param string $message
     *
     * @see \Prodii::show_admin_notice()
     */
    public static function show_admin_error($message) {
        self::show_admin_notice($message, 'error');
    }

    // endregion

    // region Settings Options

    private static function get_templates() {
        return ProdiiTemplate::get_templates();
    }

    // endregion

    public static function get_cache_dir($type = false) {
        return ProdiiCache::get_cache_dir($type);
    }

    public static function get_classes() {
        return self::$_classes;
    }

    public static function get_plugin_dir() {
        return plugin_dir_path(__FILE__);
    }

    public static function get_plugin_url() {
        return plugin_dir_url(__FILE__);
    }

    /**
     * @return array
     */
    public static function get_settings() {
        foreach (self::$_settings as $key => &$setting) {
            $setting['current'] = get_option($key);
            if (isset($setting['options']) && !is_array($setting['options']) && method_exists(get_class(), $setting['options'])) {
                $setting['options'] = call_user_func(array(get_class(), $setting['options']));
            }
        }

        return self::$_settings;
    }

    private function action_handler() {
        if ($action = prodii_get_http_post_var('prodii-action', false)) {
            switch ($action) {
                case 'refresh-object-cache':
                    $type = prodii_get_http_post_var('type', false);
                    $ids  = prodii_get_http_post_var('ids', false);
                    if ($type && $ids) {
                        switch ($type) {
                            case 'company':
                                $data = ProdiiCompany::find($ids, true);
                                $type = 'companies';
                                break;
                            case 'team':
                                $data = ProdiiTeam::find($ids, true);
                                $type = 'teams';
                                break;
                            case 'profile':
                                $data = ProdiiProfile::find($ids, true);
                                $type = 'profiles';
                                break;
                            default:
                                $data = false;
                        }

                        if ($data) {
                            $ret = array();
                            foreach ($data as $entry) {
                                $ret[] = sprintf('<b>%s</b>', $entry->get('name', true));
                            }
                            self::show_admin_success(sprintf("Refreshed cache for %s (%s)", $type, implode(', ', $ret)));
                        }
                    }

                    break;
                case 'clear-cache':
                    if (ProdiiCache::flush_cache()) {
                        self::show_admin_success('Cache cleared!');
                    } else {
                        self::show_admin_error('Error clearing cache - Please check file permissions for ' . ProdiiCache::get_cache_dir());
                    }
            }
        }
    }

    public static function get_language_levels() {
        $curl = new ProdiiCurl(get_option('prodii_access_token'), 'language-levels');

        $curl->execute();

        $code = $curl->getResponseCode();

        switch ($code) {
            case 200:
                return $curl->getResponseData();
            default:
                Prodii::show_admin_error("Prodii Error: $code " . $curl->getReponseMessage());

                return array();
        }
    }

    public static function get_skill_levels() {
        $curl = new ProdiiCurl(get_option('prodii_access_token'), 'skill-levels');

        $curl->execute();

        $code = $curl->getResponseCode();

        switch ($code) {
            case 200:
                return $curl->getResponseData();
            default:
                Prodii::show_admin_error("Prodii Error: $code " . $curl->getReponseMessage());

                return array();
        }
    }
}