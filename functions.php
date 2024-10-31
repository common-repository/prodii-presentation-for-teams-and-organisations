<?php
if (!function_exists('prodii_path_convert')) {
    function prodii_path_convert($string) {
        return preg_replace('/\\\+|\/+/', DIRECTORY_SEPARATOR, $string);
    }
}

if (!function_exists('prodii_url_convert')) {
    function prodii_url_convert($string) {
        return preg_replace('/\\\+|\/+/', '/', $string);
    }
}

if (!function_exists('register_prodii_template')) {
    function register_prodii_template($path) {
        ProdiiTemplate::register_template($path);
    }
}

if (!function_exists('prodii_get_http_post_var')) {
    function prodii_get_http_post_var($key, $default = null) {
        // Enough sanitization now?
        return preg_replace('/[^a-z0-9\,\.\s\-\æ\ø\å]/i', '', isset($_POST[$key]) ? $_POST[$key] : $default);
    }
}

if (!function_exists('prodii_get_http_post_vars')) {
    /**
     * Return unlimited amounts of POST parameters
     *
     * (Sort of like var_dump with the continuation of arguments)
     *
     * @param string|string[] $param parameter to return
     * @param string          $_     [optional]
     *
     * @return array Returns an associative array
     *               If the requested parameter isn't set, then the property is returned as false.
     *               If the requested parameter is set and has no value, then the property is returned as false
     *               If the requested paramter is set and has a value, then the property is returned as the value
     */
    function prodii_get_http_post_vars($param, $_ = '') {
        $args = func_get_args();
        $base = $_POST;

        if (is_array($args[0])) {
            $args = array_merge($args, $args[0]);
            array_shift($args);
        }

        $ret = array();
        foreach (array_values(array_unique($args)) as $arg) {
            $ret[$arg] = isset($base[$arg]) ? ($base[$arg] !== '' && !is_null($base[$arg]) ? $base[$arg] : true) : false;
        }

        foreach ($ret as $key => $value) {
            $ret[$key] = !is_bool($value) ? preg_replace('/[^a-z0-9\,\.\s\-\æ\ø\å]/i', '', $value) : $value;
        }

        return $ret;
    }
}

if (!function_exists('prodii_template_data')) {
    /**
     * @return \ProdiiTeam[]|\ProdiiCompany[]|\ProdiiProfile[]|\ProdiiBase[]
     */
    function prodii_template_data() {
        return ProdiiTemplate::get_data();
    }
}
if (!function_exists('prodii_template_type')) {
    /**
     * @return null|string
     */
    function prodii_template_type() {
        return ProdiiTemplate::get_type();
    }
}
if (!function_exists('prodii_template_args')) {
    /**
     * @return null|array
     */
    function prodii_template_args() {
        return ProdiiTemplate::get_args();
    }
}
if (!function_exists('get_prodii_template_url')) {
    function get_prodii_template_url($dir) {
        return get_home_url(null, prodii_url_convert(str_replace(prodii_path_convert(ABSPATH), '', $dir)));
    }
}