<?php
/**
 * Lib functions
 *
 * @package    report_up1urlfixe
 * @copyright  2016-2020 Silecs {@link http://www.silecs.info/societe}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once(dirname(dirname(__DIR__)).'/config.php'); // global moodle config file.


/**
 *
 * @global type $DB
 * @return array(array(string)) : table rows
 */
function report_up1urlfixe_liste() {
    global $DB, $CFG;
    $res = array();
    $sql = "SELECT c.shortname AS crsname, cd.instanceid AS crsid, cd.charvalue "
          . "FROM {customfield_data} cd "
          . "JOIN {customfield_field} cf ON (cd.fieldid = cf.id) "
          . "JOIN {course} c ON (c.id = cd.instanceid) "
          . "WHERE cf.shortname = 'up1urlfixe' AND cd.charvalue != ''  ORDER BY cd.charvalue ";
    $records = $DB->get_records_sql($sql);

    foreach ($records as $record) {
        $urlredirection = $CFG->wwwroot . '/fixe/' . $record->charvalue;
        $urlcourse = new moodle_url('/course/view.php', ['id' => $record->crsid]);
        $res[] = array (
            $record->charvalue,
            html_writer::link($urlredirection, $urlredirection),
            html_writer::link($urlcourse, $record->crsname),
        );
    }
    return $res;
}

/**
 *
 * @global type $DB
 * @return array(array(string)) : table rows
 */
function report_up1urlfixe_doublons() {
    global $DB;
    $res = array();
    $sql = "SELECT COUNT(DISTINCT cd.id) AS cnt, GROUP_CONCAT(c.shortname) AS names, GROUP_CONCAT(cd.instanceid) AS crsid, cd.charvalue "
          . "FROM {customfield_data} cd "
          . "JOIN {customfield_field} cf ON (cd.fieldid = cf.id) "
          . "JOIN {course} c ON (c.id = cd.instanceid) "
          . "WHERE cf.shortname = 'up1urlfixe' AND cd.charvalue != '' "
          . "GROUP BY cd.charvalue HAVING cnt > 1";
    $doublons = $DB->get_records_sql($sql);

    foreach ($doublons as $doublon) {
        $res[] = array (
            $doublon->cnt,
            $doublon->charvalue,
            $doublon->names,
            $doublon->crsid,
        );
    }
    return $res;
}


/**
 *
 * @global type $DB
 * @return array(array(string)) : table rows
 */
function report_up1urlfixe_supprimes() {
    global $DB;
    $res = array();
    $sql = "SELECT c.shortname AS name, cd.instanceid AS crsid, cd.charvalue "
          . "FROM {customfield_data} cd "
          . "JOIN {customfield_field} cf ON (cd.fieldid = cf.id) "
          . "LEFT JOIN {course} c ON (c.id = cd.instanceid) "
          . "WHERE cf.shortname = 'up1urlfixe' AND c.id IS NULL";
    $supprimes = $DB->get_records_sql($sql);

    foreach ($supprimes as $supprime) {
        $res[] = array (            
            $supprime->charvalue,
            $supprime->name,
            $supprime->crsid,
        );
    }
    return $res;
}