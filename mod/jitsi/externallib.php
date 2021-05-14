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

/**
 * External Web Service
 *
 * @package    mod_jitsi
 * @copyright  2021 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class mod_jitsi_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function enter_session_parameters() {
        return new external_function_parameters(
                array('user' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                      'jitsi' => new external_value(PARAM_INT, 'Jitsi session id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                      'params' => new external_value(PARAM_TEXT, 'Params', VALUE_REQUIRED, '', NULL_NOT_ALLOWED))
        );
    }

    public static function exit_session_parameters() {
        return new external_function_parameters(
            array('user' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'jitsi' => new external_value(PARAM_INT, 'Jitsi session id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'params' => new external_value(PARAM_TEXT, 'Params', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)));
    }

    public static function left_session_parameters() {
        return new external_function_parameters(
            array('user' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'jitsi' => new external_value(PARAM_INT, 'Jitsi session id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'params' => new external_value(PARAM_TEXT, 'Params', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)));
    }

    public static function joined_session_parameters() {
        return new external_function_parameters(
            array('user' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'jitsi' => new external_value(PARAM_INT, 'Jitsi session id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'params' => new external_value(PARAM_TEXT, 'Params', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)));
    }

    public static function state_record_parameters() {
        return new external_function_parameters(
            array('jitsi' => new external_value(PARAM_INT, 'Jitsi session id', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                  'state' => new external_value(PARAM_TEXT, 'State', VALUE_REQUIRED, '', NULL_NOT_ALLOWED)));
    }

    /**
     * Returns welcome message
     * @return string usernname pic message
     */
    public static function enter_session($user, $jitsi, $paramspassed) {
        global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::enter_session_parameters(),
                array('user' => $user, 'jitsi' => $jitsi, 'params' => $paramspassed));

        $interaction = new stdClass();
        $interaction->userid = $user;
        $interaction->jitsi = $jitsi;
        $interaction->params = $paramspassed;
        $interaction->action = 'enter';
        $interaction->date = time();
        $DB->insert_record('jitsi_interactions', $interaction);
        return 'save';
    }

    public static function exit_session($user, $jitsi, $paramspassed) {
        global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::exit_session_parameters(),
                array('user' => $user, 'jitsi' => $jitsi, 'params' => $paramspassed));
        $interaction = new stdClass();
        $interaction->jitsi = $jitsi;
        $interaction->userid = $user;
        $interaction->action = 'exit';
        $interaction->date = time();
        $DB->insert_record('jitsi_interactions', $interaction);
        return 'save';
    }

    public static function left_session($user, $jitsi, $paramspassed) {
        global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::left_session_parameters(),
                array('user' => $user, 'jitsi' => $jitsi, 'params' => $paramspassed));
        $interaction = new stdClass();
        $interaction->jitsi = $jitsi;
        $interaction->userid = $user;
        $interaction->params = $paramspassed;
        $interaction->action = 'left';
        $interaction->date = time();
        $DB->insert_record('jitsi_interactions', $interaction);
        return 'save';
    }

    public static function joined_session($user, $jitsi, $paramspassed) {
        global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::joined_session_parameters(),
                array('user' => $user, 'jitsi' => $jitsi, 'params' => $paramspassed));
        $interaction = new stdClass();
        $interaction->jitsi = $jitsi;
        $interaction->userid = $user;
        $interaction->params = $paramspassed;
        $interaction->action = 'joined';
        $interaction->date = time();
        $DB->insert_record('jitsi_interactions', $interaction);
        return 'save';
    }

    public static function state_record($jitsi, $state) {
        global $USER, $DB;

        // Parameter validation.
        // REQUIRED.
        $params = self::validate_parameters(self::state_record_parameters(),
                array('jitsi' => $jitsi, 'state' => $state));
        $jitsiob = $DB->get_record('jitsi', array('id' => $jitsi));
        if ($state == 1) {
            $jitsiob->recording = 'recording';
        } else {
            $jitsiob->recording = 'stop';
        }
        $DB->update_record('jitsi', $jitsiob);
        return 'recording'.$jitsiob->recording;
    }


    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function enter_session_returns() {
        return new external_value(PARAM_TEXT, 'Enter session');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function exit_session_returns() {
        return new external_value(PARAM_TEXT, 'Exit session');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function left_session_returns() {
        return new external_value(PARAM_TEXT, 'Exit session');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function joined_session_returns() {
        return new external_value(PARAM_TEXT, 'Exit session');
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function state_record_returns() {
        return new external_value(PARAM_TEXT, 'State record session');
    }
}
