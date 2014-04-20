<?php

/**
 * Mobile Demo class for smart phones (ex: Android).
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2003-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mobile_demo/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\mobile_demo;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ?
    getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';

require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('mobile_demo');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;
use \clearos\apps\base\OS as OS;
use \clearos\apps\base\Webconfig as Webconfig;
use \clearos\apps\network\Hostname as Hostname;
use \clearos\apps\mobile_demo\Mobile_Demo_Service as Mobile_Demo_Service;

clearos_load_library('base/Engine');
clearos_load_library('base/File');
clearos_load_library('base/OS');
clearos_load_library('base/Webconfig');
clearos_load_library('network/Hostname');
clearos_load_library('mobile_demo/Mobile_Demo_Service');

// Exceptions
//-----------

use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\File_Not_Found_Exception as File_Not_Found_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/File_Not_Found_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mobile Demo class for smart phones (ex: Android).
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2003-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mobile_demo/
 */

class Mobile_Demo extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const PROTOCOL_VERSION      =   1;

    const RESULT_EXCEPTION      =  -1;
    const RESULT_SUCCESS        =   0;
    const RESULT_UNKNOWN        =   1;
    const RESULT_ACCESS_DENIED  =   2;

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Mobile Demo constructor.
     *
     * @return void
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Login method.
     *
     * @return boolean
     */

    public function login($username, $password)
    {
        $framework = &get_instance();
        if ($framework->login_session->authenticate($username, $password)) {
            // Start an authenticated session, override default session
            // expiration value to 1 week (default: 2 hours).
            $framework->login_session->start_authenticated(
                $username, 24 * 3600 * 7
            );
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Logout method.
     *
     * @return void
     */

    public function logout()
    {
        $framework = &get_instance();
        $framework->login_session->stop_authenticated();
    }

    /**
     * Check if user is logged in.
     *
     * @return boolean
     */

    public function is_logged_in()
    {
        $framework = &get_instance();
        return $framework->login_session->is_authenticated();
    }

    /**
     * Return basic system information.
     *
     * @return array
     */

    public function get_system_info($last_sample = -1)
    {
        $os = new OS;
        $hostname = new Hostname;

        $data = array();
        $data['version'] = self::PROTOCOL_VERSION;
        $data['name'] = $os->get_name();
        $data['hostname'] = $hostname->get();
        $data['release'] = $os->get_version();
        $data['time'] = strftime('%s');
        $data['time_locale'] = strftime('%c');

        $data['samples'] = Mobile_Demo_Service::get_samples($last_sample);

        return $data;
    }
}

// vi: expandtab shiftwidth=4 softtabstop=4 tabstop=4
