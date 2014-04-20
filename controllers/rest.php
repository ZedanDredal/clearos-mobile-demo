<?php

/**
 * Mobile Demo REST controller.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mobile_demo/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\OS as OS;
use \clearos\apps\mobile_demo\Mobile_Demo as Mobile_Demo_Library;
use \clearos\apps\network\Network as Network;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mobile Demo REST controller.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mobile_demo/
 */

class Rest extends ClearOS_Controller
{
    private $data = array();

    /**
     * Rest constructor.
     */

    function __construct()
    {
        // Load libraries
        //---------------

        $this->load->library('mobile_demo/Mobile_Demo');
        $this->lang->load('mobile_demo');

        // Send headers
        //-------------

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: application/json');

        // Set default JSON data
        //----------------------

        $this->data['result'] = Mobile_Demo_Library::RESULT_UNKNOWN;
        $this->data['exception'] = null;
    }

    /**
     * Rest destructor.
     */

    function __destruct()
    {
        if (ob_get_length() > 0) ob_clean();
        printf("%s\n", json_encode($this->data));
    }

    /**
     * Rest index.
     * Authenticate via HTTP POST.
     */

    public function index()
    {
        try {
            if ($this->mobile_demo->is_logged_in() === false) {
                if (!$this->input->post('submit')) {
                    $this->data['result'] =
                        Mobile_Demo_Library::RESULT_ACCESS_DENIED;
                }
                else if (!$this->mobile_demo->login(
                    $this->input->post('username'),
                    $this->input->post('password'))) {
                    $this->data['result'] =
                        Mobile_Demo_Library::RESULT_ACCESS_DENIED;
                }
                else $this->data['result'] =
                    Mobile_Demo_Library::RESULT_SUCCESS;
            }
            else $this->data['result'] =
                Mobile_Demo_Library::RESULT_SUCCESS;
        } catch (Exception $e) {
            $this->data['result'] = Mobile_Demo_Library::RESULT_EXCEPTION;
            $this->data['exception'] = sprintf(
                '[%d] %s: %s', $e->getCode(), get_class($e), $e->getMessage());
        }
    }

    /**
     * Check session credentials
     * Simply checks if we're logged in and if not, returns access denied.
     */

    private function authenticate()
    {
        if ($this->mobile_demo->is_logged_in() === false) {
            $this->data['result'] = Mobile_Demo_Library::RESULT_ACCESS_DENIED;
            exit();
        }
    }

    /**
     * Log-out of session
     * This will invalidate the current session if it exists.  Used for
     * debugging the Android SyncAdapter/AccountManager implementation.
     */
    public function logout()
    {
        $this->mobile_demo->logout();
        $this->data['result'] = Mobile_Demo_Library::RESULT_ACCESS_DENIED;
        exit();
    }

    /**
     * Example REST method.
     */

    public function method()
    {
        $this->authenticate();

        $this->data['result'] = Mobile_Demo_Library::RESULT_SUCCESS;
        $this->data['params'] = func_get_args();
    }

    /**
     * Get system information.
     */

    public function system_info($last_sample = -1)
    {
        $this->authenticate();

        try {
            $this->data['result'] = Mobile_Demo_Library::RESULT_SUCCESS;
            $this->data['data'] =
                $this->mobile_demo->get_system_info($last_sample);
        } catch (Exception $e) {
            $this->data['result'] = Mobile_Demo_Library::RESULT_EXCEPTION;
            $this->data['exception'] = sprintf(
                '[%d] %s: %s', $e->getCode(), get_class($e), $e->getMessage());
        }
    }
}

// vi: expandtab shiftwidth=4 softtabstop=4 tabstop=4
