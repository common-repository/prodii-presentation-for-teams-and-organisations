<?php

class ProdiiProfile
    extends ProdiiBase {
    protected static $_cache_dir = 'profile';
    protected static $_list_command = 'list-profile';
    protected static $_command = 'profile';

    public function get_industries() {
        $positions = $this->get('positions');
        if ($positions) {
            $industries = array();
            foreach ($positions as $position) {
                if ($position['startdate']) {
                    $cur_time = isset($industries[ $position['industry'] ]) ? $industries[ $position['industry'] ] : 0;
                    $start = $position['startdate'];
                    $end = !is_null($position['enddate']) ? $position['enddate'] : (new DateTime())->format('U');
                    $time = $end - $start;
                    $industries[ $position['industry'] ] = $cur_time + $time;
                }
            }

            $ret = array();
            foreach ($industries as $key => $val) {
                $diff = ((new DateTime('@0'))->diff(new DateTime("@$val")));
                $years = ($years = $diff->format('%y')) != 0 ? ($years == 1 ? "$years year" : "$years years") : '';
                $months = ($months = $diff->format('%m')) != 0 ? ($months == 1 ? "$months month" : "$months months") : '';

                $ret[ $key ] = array(
                    'time'     => $val,
                    'timetext' => implode(' ', array_filter(array($years, $months))),
                );
            }

            return $ret;
        }

        return array();
    }
}