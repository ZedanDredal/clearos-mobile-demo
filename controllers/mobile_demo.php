<?php

/**
 * Mobile Demo controller.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
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

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mobile Demo controller.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mobile_demo/
 */

class Mobile_Demo extends ClearOS_Controller
{
    /**
     * Login index.
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('mobile_demo');

        // Load view data
        //---------------

        $views = array('mobile_demo/server', 'mobile_demo/info');
        $this->page->view_forms($views, lang('mobile_demo_mobile_demo'));
    }
}

// vi: expandtab shiftwidth=4 softtabstop=4 tabstop=4
