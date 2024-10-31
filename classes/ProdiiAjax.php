<?php
/**
 * Created by PhpStorm.
 * User: ExeQue
 * Date: 15-04-2018
 * Time: 16:23
 */

class ProdiiAjax {
    private static $_hooked = false;
    private $has_error = false;

    public function __construct() {
        if (!self::$_hooked) {
            self::$_hooked = true;
            try {
                $rfc = new ReflectionClass($this);
                foreach ($rfc->getMethods() as $method) {
                    if ($method->name !== '__construct' && !$method->isStatic() && $method->isPublic()) {
                        $name = strtolower(preg_replace('/\B([A-Z]{1})/', '_$1', $method->name));

                        add_action("wp_ajax_prodii_$name", array($this, $name));
                        add_action("wp_ajax_nopriv_prodii_$name", array($this, $name));
                    }
                }
            }
            catch (Exception $exception) {
                // Never gonna get here... I promise...!
            }
        }
    }

    public function get_html() {
        $args = prodii_get_http_post_vars('template', 'type', 'ids');
        $sc = new ProdiiShortcode();
        ob_start();
        switch ($args['type']) {
            case 'team':
                $this->has_error = !!$sc->load(array('type' => 'team', 'template' => $args['template'], 'id' => $args['ids']));
                $result = ob_get_clean();
                break;
            case 'company':
                $this->has_error = !!$sc->load(array('type' => 'company', 'template' => $args['template'], 'id' => $args['ids']));
                $result = ob_get_clean();
                break;
            case 'profile':
                $this->has_error = !!$sc->load(array('type' => 'profile', 'template' => $args['template'], 'id' => $args['ids']));
                $result = ob_get_clean();
                break;
            default:
                $result = false;
                break;
        }

        self::output_json($result !== false && !$this->has_error ? 200 : 422, $result !== false ? $result : "Missing params: " . implode(', ', array_keys($args, false)));
    }

    public static function output_json($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(array('code' => $code, 'data' => $data), constant('JSON_PRETTY_PRINT'));
        die();
    }
}

new ProdiiAjax();