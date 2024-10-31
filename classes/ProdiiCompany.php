<?php

/**
 * Class ProdiiCompany
 *
 * @property int       $id
 * @property \DateTime $created
 * @property string    customizedurl
 * @property array     $image
 * @property array     $location
 * @property string    $longdesc
 * @property int[]     $members
 * @property string    $name
 * @property int       $owner
 * @property bool      $publish
 * @property string    $shortdesc
 * @property array     $socialnetworks
 * @property int[]     $teams
 * @property string    $telephone
 * @property string    $email
 * @property int       $status
 */
class ProdiiCompany extends ProdiiBase {
    protected static $_cache_dir    = 'company';
    protected static $_list_command = 'list-company';
    protected static $_command      = 'company';

    /**
     * @return ProdiiBase[]|ProdiiTeam[]
     */
    public function get_teams() {
        return ProdiiTeam::find($this->teams['value']);
    }

    /**
     * @return ProdiiBase[]|ProdiiProfile[]
     */
    public function get_members() {
        $teams      = $this->get_teams();
        $tmpmembers = array();

        foreach ($teams as $team) {
            $tmpmembers = array_unique(array_merge($tmpmembers, $team->members['value']));
        }
        $members = ProdiiProfile::find(implode(',', $tmpmembers));

        return $members;
    }
}