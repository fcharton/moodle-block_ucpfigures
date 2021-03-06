<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Block displaying stats about the site.
 *
 * @package    block_ucpfigures
 * @author     Laurent Guillet <laurent.guillet@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 * File : block_ucpfigures.php
 * Block class definition
 *
 */

defined('MOODLE_INTERNAL') || die();

class block_ucpfigures extends block_base {

    public function init() {

        $this->title = get_string('pluginname', 'block_ucpfigures');
    }

    public function get_content() {

        global $CFG, $DB;
        if ($this->content !== null) {

            return $this->content;
        }
        if (empty($this->instance)) {

            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!empty($this->config->text)) {

            $this->content->text = $this->config->text;
        }
        $this->content->text = '';

        if (isloggedin()) {

            $sqlenligne = "SELECT COUNT( DISTINCT userid) FROM {sessions}";
            $resenligne = $DB->count_records_sql($sqlenligne);
            $this->content->text  .= "<strong> $resenligne</strong> connectés<br>";

            $sqlcourse = "SELECT COUNT( DISTINCT c.id) FROM {course} AS c WHERE idnumber LIKE '$CFG->yearprefix-%'";
            $rescourse = $DB->count_records_sql($sqlcourse);
            $nextyear = $CFG->thisyear + 1;
            $this->content->text .= "<strong> $rescourse</strong> cours $CFG->thisyear-$nextyear<br>";

            $nbdistinctteachers = $DB->get_record('block_ucpfigures_stats', array('name' => 'distinctteachers'))->value;

            $this->content->text .= "<strong> $nbdistinctteachers</strong> enseignants<br>";

            $context = context_system::instance();

            if (has_capability('block/ucpfigures:viewinfo', $context)) {

                    $this->content->text .= "<br><a href = '$CFG->wwwroot/blocks/ucpfigures/figures.php'>"
                            . "Plus de chiffres...</a>";
            }

            if (!empty($this->config->text)) {

                $this->content->text .= $this->config->text;
            }
            return $this->content;
        }
    }

    public function applicable_formats() {

        return array('all' => true, 'site' => true, 'site-index' => true, 'course-view' => true,
            'course-view-social' => false, 'mod' => true, 'my' => true, 'mod-quiz' => false);
    }

    public function instance_allow_multiple() {

          return false;
    }

    public function has_config() {

        return true;
    }
}
