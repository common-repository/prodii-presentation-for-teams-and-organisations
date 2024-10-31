<?php

/**
 * Class ProdiiTeam
 *
 * @property int       $id
 * @property \DateTime $created
 * @property string    customizedurl
 * @property int       $companyid
 * @property string    $longdesc
 * @property int[]     $members
 * @property string    $name
 * @property int       $owner
 * @property bool      $publish
 * @property int       $shortdesc
 * @property int       $status
 */
class ProdiiTeam
    extends ProdiiBase {
    protected static $_cache_dir = 'team';
    protected static $_list_command = 'list-team';
    protected static $_command = 'team';

    /**
     * @return \ProdiiProfile[]
     */
    public function get_members() {
        $members = ProdiiProfile::find($this->get('members'));

        usort($members, function ($a, $b) {
            if ($this->get('owner') === $a->get('id')) {
                return -1;
            }

            return strcmp($a->get('name'), $b->get('name'));
        });

        return $members;
    }

    public function get_languages() {
        $members = $this->get_members();
        $langs = array();
        foreach ($members as $member) {
            foreach ($member->get('languages') as $lang) {
                if (!in_array($lang['name'], $langs)) {
                    $langs[] = $lang['name'];
                }
            }
        }

        sort($langs, SORT_ASC);

        return $langs;
    }

    public function get_company() {
        return ProdiiCompany::find($this->get('companyid'))[ $this->get('companyid') ];
    }

    public function get_positions() {
        $members = $this->get_members();
        $positions = array();
        foreach ($members as $member) {
            if ($member->get('positions')) {
                $positions = array_merge($positions, $member->get('positions'));
            }
        }

        return $positions;
    }

    public function get_industries() {
        $positions = $this->get_positions();
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
}