<?php

class ProdiiBase {
    protected static $_cache_dir = false;
    protected static $_list_command = false;
    protected static $_command = false;
    public $_data = array();

    public function __construct($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $this->after_load();
    }

    public function after_load() {
        $fields = array();
        foreach (array_keys($this->_data) as $key) {
            if (strpos($key, 'location')) {
                $fields[] = $key;
            }
        }
        foreach ($fields as $loc) {
            if ($location = $this->get($loc)) {
                $this->_data[ $loc ]['value']['addresscomponents'] = json_decode($this->get($loc)['addresscomponents'], true);
                if ($this->_data[ $loc ]['value']['addresscomponents']) {
                    $arr = array(
                        'street_number' => null,
                        'route'         => null,
                        'locality'      => null,
                        'country'       => null,
                        'postal_code'   => null,
                    );
                    foreach ($this->_data[ $loc ]['value']['addresscomponents'] as $comp) {
                        $arr[ $comp['types'][0] ] = array(
                            'long'  => $comp['long_name'],
                            'short' => $comp['short_name'],
                        );
                    }

                    $this->_data[ $loc ]['value']['variants'] = array(
                        'zip_city'         => trim("{$arr['country']['short']}-{$arr['postal_code']['long']}  {$arr['locality']['long']}"),
                        'zip_city_country' => trim("{$arr['postal_code']['long']} {$arr['locality']['long']}, {$arr['country']['long']}"),
                        'city_country'     => trim("{$arr['locality']['long']}, {$arr['country']['long']}"),
                    );
                }
            }
        }
    }

    public function __get($name) {
        return isset($this->{$name}) ? $this->{$name} : isset($this->_data[ $name ]) ? $this->_data[ $name ] : null;
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            $this->_data[ $name ] = $value;
        }
    }

    public function get($property, $override_privacy = null) {
        if (is_null($override_privacy)) {
            $override_privacy = Prodii::get_default_privacy_override();
        }

        $data = $this->{$property};

        if (is_array($data)) {
            return $data['public'] ? $data['value'] : $override_privacy ? $data['value'] : false;
        }

        return $this->{$property};
    }

    public static function get_cache_dir() {
        /** @var self $class */
        $class = get_called_class();

        return $class::$_cache_dir;
    }

    /**
     * @param      $ids
     * @param bool $force_reload
     *
     * @return $this[]|self[]
     */
    final public static function find($ids, $force_reload = false) {
        /** @var self $caller */
        $caller = get_called_class();

        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ret = array();

        foreach ($ids as $id) {
            if (!$force_reload && !ProdiiCache::cache_entry_too_old($caller::$_cache_dir, $id)) {
                $data[ $id ] = ProdiiCache::get($caller::$_cache_dir, $id)[ $id ];
            } else {

                $curl = new ProdiiCurl(get_option('prodii_access_token'), $caller::$_command, $id);

                $curl->execute();

                $code = $curl->getResponseCode();

                switch ($code) {
                    case 200:
                        $data = $curl->getResponseData();
                        break;
                    default:
                        Prodii::show_admin_error("Prodii Error: $code " . $curl->getReponseMessage());

                        return array();
                }

                if (!ProdiiCache::save_to_cache($caller::$_cache_dir, $id, $data)) {
                    Prodii::show_admin_error("Failed to reload cache for {$caller::$_cache_dir} $id");
                }
            }

            $ret[ $id ] = new $caller($data[ $id ]);
        }

        return !empty($ret) ? $ret : null;
    }

    /**
     * Get a list of available entries for the given type
     *
     * @return array|null
     */
    final public static function get_list() {
        /** @var self $caller */
        $caller = get_called_class();

        $curl = new ProdiiCurl(get_option('prodii_access_token'), $caller::$_list_command);

        $curl->execute();

        $code = $curl->getResponseCode();

        switch ($code) {
            case 200:
                return $curl->getResponseData();
            default:
                Prodii::show_admin_error("Prodii Error: $code - " . $curl->getReponseMessage());

                return array();
        }
    }

    /**
     * Returns the owner of the specified key
     * Use this method with caution, as the data is requested from Prodii.com every time and isn't cached on the server
     *
     * @param $key
     *
     * @return \ProdiiProfile
     */
    final public static function get_owner($key) {
        $curl = new ProdiiCurl($key, 'key-owner');

        $curl->execute();

        $code = $curl->getResponseCode();

        switch ($code) {
            case 200:
                $data = $curl->getResponseData();
                foreach ($data as $entry) {
                    return new ProdiiProfile($entry);
                };

                return new ProdiiProfile(array());
            default:
                Prodii::show_admin_error("Prodii Error: $code - " . $curl->getReponseMessage());

                return new ProdiiProfile(array());
        }
    }

    /**
     * @return string
     */
    final public function get_type() {
        switch (get_called_class()) {
            case 'ProdiiCompany':
                return Prodii::COMPANY;
            case 'ProdiiTeam':
                return Prodii::TEAM;
            case 'ProdiiProfile':
                return Prodii::PROFILE;
        }
    }

    /**
     * Checks if the data is of a certain type
     *
     * @param string $type Type to check
     *
     * @see \Prodii::COMPANY
     * @see \Prodii::TEAM
     * @see \Prodii::PROFILE
     *
     * @return bool
     */
    final public function is_type($type) {
        return in_array($type, array(Prodii::COMPANY, Prodii::TEAM, Prodii::PROFILE));
    }

    final public function is_valid() {
        if ($this->status === 200) {
            return true;
        }

        return false;
    }
}