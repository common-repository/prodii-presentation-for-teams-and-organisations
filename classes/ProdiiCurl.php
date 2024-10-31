<?php

/**
 * Class ProdiiCurl
 */
final class ProdiiCurl {
    private static $_endpoint = 'https://prodii.com/common/api/apihandler.php';
    //    private static $_endpoint = 'http://dev.prodii.com/common/api/apihandler.php';
    private static $_ua            = 'prodii-wp-plugin';
    private        $_key;
    private        $_command;
    private        $_ids;
    private        $_response_data;
    private static $_cache_max_age = 0;

    private static function get_cache_dir() {
        if (!file_exists(prodii_path_convert(Prodii::get_cache_dir() . 'curl/'))) {
            mkdir(prodii_path_convert(Prodii::get_cache_dir() . 'curl/'), 0777);
        }

        return prodii_path_convert(Prodii::get_cache_dir() . 'curl/');
    }

    private static function cache_entry_exists($url) {
        $url  = hash('sha256', $url);
        $dir  = self::get_cache_dir();
        $path = "{$dir}{$url}";
        $now  = (int)(new DateTime())->format('U');
        if (file_exists("{$dir}{$url}") && $now - filemtime($path) <= (self::$_cache_max_age)) {
            return file_get_contents($path);
        }

        return false;
    }

    private static function add_to_cache($url, $data) {
        $url = hash('sha256', $url);
        $dir = self::get_cache_dir();

        return file_put_contents("{$dir}{$url}", $data);
    }

    public function __construct($key, $command, $ids = null) {
        $this->_key     = $key;
        $this->_command = $command;
        $this->_ids     = is_array($ids) ? implode(',', $ids) : is_scalar($ids) ? $ids : null;
    }

    public function execute() {
        if ($this->_response_data) {
            Prodii::show_admin_error('ProdiiCurl can only be executed once per instance');

            return false;
        }
        $params = array('action' => $this->_command, 'key' => $this->_key);

        if ($this->_ids) {
            $params['ids'] = $this->_ids;
        }

        $url = sprintf('%s?%s', self::$_endpoint, self::format_post_fields($params));

        if ($data = self::cache_entry_exists($url)) {
            $this->_response_data = $data;

            return $this;
        }

        $response = wp_remote_post(
            self::$_endpoint, array(
                                'body' => $params
                            )
        );

        if (is_object($response) && get_class($response) === 'WP_Error') {
            Prodii::show_admin_error(sprintf('<h3>Prodii</h3><p>%s - %s</p>', $response->get_error_code()));
            return $this;
        } else {
            $response = wp_remote_retrieve_body($response);
            if($response !== '') {
                $this->_response_data = $response;
            }
        }

        if ($this->_response_data) {
            self::add_to_cache($url, $this->_response_data);
        }

        return $this;
    }

    private static function format_post_fields($fields) {
        $ret = array();
        foreach ($fields as $key => $value) {
            $ret[] = "$key=" . urlencode($value);
        }

        return implode('&', $ret);
    }

    private function getJSONData() {
        return json_decode($this->_response_data, true);
    }

    public function getResponseData() {
        $data = $this->getJSONData();

        return isset($data['data']) ? $data['data'] : null;
    }

    public function getResponseCode() {
        $data = $this->getJSONData();

        return isset($data['response_code']) ? $data['response_code'] : 500;
    }

    public function getReponseMessage() {
        $data = $this->getJSONData();

        return isset($data['response_message']) ? $data['response_message'] : null;
    }
}
