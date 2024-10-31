<?php

class ProdiiShortcode {
    /**
     * @var null|\ProdiiTemplate
     */
    protected static $_template;
    protected static $_type;
    protected static $_ids;
    protected static $_data_collection;
    protected static $_tmp_data = null;
    protected static $_multi = false;
    protected static $_args = array();

    public function load($atts) {
        self::$_type = $atts['type'];
        self::$_template = ProdiiTemplate::get_template($atts['template']);
        self::$_ids = $atts['id'];
        self::$_data_collection = $this->fetch_data();
        self::$_args = $atts;

        self::$_ids = !is_null(self::$_ids) ? array_filter(array_unique(preg_split('/\,|\||\.|\//', self::$_ids)), function ($e) {
            return is_numeric($e);
        }) : null;

        self::$_multi = isset($atts['multi']);

        if ($this->is_valid()) {
            return $this->render();
        } else {
            return false;
        }
    }

    protected function render() {
        ob_start();
        self::$_template->load_assets();
        if (self::$_multi) {
            ob_start();
            self::$_template->render(self::$_data_collection, self::$_type, 'multi', self::$_args);
            echo sprintf('<div class="prodii-wrapper" data-prodii-type="%s" data-prodii-multi>%s</div>', self::$_type, ob_get_clean());
        } else {
            foreach (self::$_ids as $id) {
                ob_start();
                self::$_template->render(self::$_data_collection, self::$_type, $id, self::$_args);
                echo sprintf('<div class="prodii-wrapper" data-prodii-type="%s" data-prodii-id="%s">%s</div>', self::$_type, $id, ob_get_clean());
            }
        }

        self::$_tmp_data = null;

        return ob_get_clean();
    }

    protected function fetch_data() {
        switch (strtolower(self::$_type)) {
            case 'company':
                $data = ProdiiCompany::find(self::$_ids);
                break;
            case 'team':
                $data = ProdiiTeam::find(self::$_ids);
                break;
            case 'profile':
                $data = ProdiiProfile::find(self::$_ids);
                break;
            default:
                return null;
        }

        return array_filter($data, function ($e) {
            /** @var \ProdiiBase $e */
            return $e->is_valid();
        });
    }

    protected function is_valid() {
        if (is_null(self::$_type)) {
            self::show_shortcode_error('Prodii shortcode is missing required parameter "type" - eg. [prodii type="team" ... ]');

            return false;
        } elseif ((is_object(self::$_template) && get_class(self::$_template) !== 'ProdiiTemplate') || !is_object(self::$_template)) {
            self::show_shortcode_error('Prodii template "' . self::$_template . '" isn\'t defined');

            return false;
        } elseif (is_null(self::$_ids)) {
            self::show_shortcode_error('Prodii shortcode is missing required parameter "id" - eg. [prodii id="123" ... ]');

            return false;
        } elseif (empty(self::$_ids)) {
            self::show_shortcode_error('Prodii shortcode parameter "id" must be numeric');

            return false;
        } elseif (is_null(self::$_data_collection)) {
            self::show_shortcode_error('An error occurred while fetching data for Prodii template');

            return false;
        } elseif (empty(self::$_data_collection)) {
            self::show_shortcode_error(sprintf('No data returned for Prodii shortcode with type="%s" and id="%s"', self::$_type, implode(',', self::$_ids)));

            return false;
        }

        return true;
    }

    protected static function show_shortcode_error($message) {
        if (current_user_can('publish_posts') || current_user_can('edit_posts') || current_user_can('publish_pages') || current_user_can('edit_pages')) {
            $edit_link = get_edit_post_link(get_the_ID());
            echo "<div style='background: red; color: white; padding: 5px; border-radius: 3px; box-shadow: 2px 2px 5px rgba(0,0,0,0.5);'>$message <br><small style='color: white;'>This error is only visible to user who can edit and/or publish posts or pages - <a style='color: white; text-decoration:underline;' href='$edit_link'>Edit</a></small></div>";
        }
    }

    public static function get_data() {
        return self::$_tmp_data;
    }

    public static function get_type() {
        return self::$_type;
    }
}

add_shortcode('prodii', array(new ProdiiShortcode(), 'load'));