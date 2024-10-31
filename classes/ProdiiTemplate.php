<?php

class ProdiiTemplate {
    /** @var self[] */
    private static $_templates = array();
    private static $_data = null;
    private static $_type = null;
    private static $_args = null;
    private $path, $slug, $title;
    private $_assets_loaded = false;

    public static function register_template($path) {
        $template = new self($path);
        if (!$template->valid()) {
            Prodii::show_admin_warning("Prodii Template at '$path' isn't a valid Prodii template - Please refer to the Reference in the Prodii admin menu");
        } else {
            self::$_templates[ $template->get_slug() ] = $template;
        }
    }

    public static function get_templates() {
        return self::$_templates;
    }

    public static function get_data() {
        return self::$_data;
    }

    public static function get_type() {
        return self::$_type;
    }

    public static function get_args() {
        return self::$_args;
    }

    public static function get_template($slug) {
        return isset(self::$_templates[ $slug ]) ? self::$_templates[ $slug ] : $slug;
    }

    private function __construct($path) {
        $path .= in_array($path[ strlen($path) - 1 ], array('\\', '/')) ? '' : DIRECTORY_SEPARATOR;
        $this->path = prodii_path_convert($path);
    }

    public function render($data = null, $type = '', $id = '', $args = null) {
        self::$_data = $data;
        self::$_type = $type;
        self::$_args = $args;
        include $this->get_template_file($type, $id);
        self::$_data = null;
        self::$_type = null;
        self::$_args = null;
    }

    public function valid() {
        if (file_exists($this->get_path()) && $this->get_title() && $this->get_slug()) {
            return true;
        }

        return false;
    }

    public function load_assets() {
        if (!$this->_assets_loaded) {
            $types = array(
                'scripts' => array_map('trim', array_unique(explode(',', $this->get_template_data()['scripts']))),
                'styles'  => array_map('trim', array_unique(explode(',', $this->get_template_data()['styles']))),
            );

            wp_enqueue_script('prodii-googlemap-places', 'https://maps.googleapis.com/maps/api/js?libraries=places&amp;language=en', false, '3');
            wp_enqueue_script('prodii-client-script', Prodii::get_plugin_url() . 'assets/dist/js/client.js', array('jquery', 'prodii-googlemap-places'));
            wp_localize_script('prodii-client-script', 'prodii_settings', array('ajax_url' => admin_url('admin-ajax.php')));

            $version = preg_replace('/[^a-z0-9]/i', '.', $this->get_template_data()['version']);

            foreach ($types as $type => $assets) {
                $prev = array();
                foreach ($assets as $asset) {
                    $path = prodii_path_convert($this->path . $asset);
                    $slug = $this->slug . '-' . preg_replace('/[^a-z0-1]/i', '-', $asset);
                    $url = get_prodii_template_url($path);
                    $prev[] = $slug;
                    if (file_exists($path)) {
                        if ($type === 'scripts') {
                            wp_enqueue_script($slug, $url, array('prodii-client-script'), $version, true);
                        } else {
                            wp_enqueue_style($slug, $url, array(), $version);
                        }
                    }
                }
            }
        }
    }

    private static function find_assets($path, $types = array('css', 'js')) {
        $files = array();
        foreach ($types as $type) {
            $files = array_merge($files, glob("{$path}*.{$type}"));
            $dirs = glob("{$path}*", GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $files = array_merge($files, self::find_assets(prodii_path_convert($dir . '/'), array($type)));
            }
        }

        return $files;
    }

    public function get_path() {
        return $this->path . "template.php";
    }

    public function get_template_file($type = '', $id = '') {
        if ($this->template_file_exists($type, $id)) {
            return $this->template_file($type, $id);
        } elseif ($this->template_file_exists($type)) {
            return $this->template_file($type);
        }

        return $this->template_file();
    }

    private function template_file_exists($type, $id = '') {
        return file_exists($this->template_file($type, $id));
    }

    private function template_file($type = '', $id = '') {
        $type = $type !== '' ? "-$type" : '';
        $id = $id !== '' ? "-$id" : '';

        return $this->path . "template$type$id.php";
    }

    public function get_title() {
        return isset($this->title) ? $this->title : $this->title = $this->get_template_data()['title'];
    }

    public function get_slug() {
        return isset($this->slug) ? $this->slug : $this->slug = $this->get_template_data()['slug'];
    }

    public function get_template_data() {
        if (isset($this->__template_data)) {
            return $this->__template_data;
        }

        return $this->__template_data = Prodii::get_file_data_header($this->get_template_file(), array(
            'title'   => 'Template Name',
            'slug'    => 'Template Slug',
            'styles'  => 'Styles',
            'scripts' => 'Scripts',
            'version' => 'Version',
        ));
    }

    public function get_url() {
        return get_site_url() . '/' . prodii_url_convert(str_replace(prodii_path_convert(ABSPATH), '', prodii_path_convert($this->path)));
    }
}