<?php

/**
 * Mobile Demo Service class.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/base/
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

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('base');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\framework\Logger as Logger;
use \clearos\apps\base\Service as Service;
use \clearos\apps\base\File as File;
use \clearos\apps\network\Iface_Manager as Iface_Manager;

clearos_load_library('base/Service');
clearos_load_library('base/File');
clearos_load_library('network/Iface_Manager');

// Exceptions
//-----------

use \clearos\apps\base\Engine_Exception as Engine_Exception;
use \clearos\apps\base\Validation_Exception as Validation_Exception;
use \clearos\apps\base\File_Exception as File_Exception;

clearos_load_library('base/Engine_Exception');
clearos_load_library('base/Validation_Exception');
clearos_load_library('base/File_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mobile Demo Service class.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/base/
 */

class Mobile_Demo_Service extends Service
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    // Tag used for logging.
    const TAG = 'mobile-demo';

    // Location to save JSON encoded sample data.
    const FILE_STATE = '/var/state/webconfig/mobile-demo.state';

    // History of samples to store.
    // 900 (15 minutes of history) / INTERVAL_UPDATE
    const MAX_SAMPLES = 225;

    // Update frequency in seconds.
    const INTERVAL_UPDATE = 4;

    // Load average data
    const FILE_PROC_LOADAVG     = '/proc/loadavg';

    // Memory info source
    const FILE_PROC_MEMINFO     = '/proc/meminfo';

    // Interface RX/TX byte counters
    const FORMAT_NET_RX_BYTES   = '/sys/class/net/%s/statistics/rx_bytes';
    const FORMAT_NET_TX_BYTES   = '/sys/class/net/%s/statistics/tx_bytes';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////


    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Mobile Demo Service constructor.
     *
     * @param int $argc argument count.
     * @param array $argv parameter array.
     */

    public function __construct($argc = 0, $argv = array())
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct($argc, $argv);
    }

    /**
     * Service entry point.
     *
     */

    public function entry()
    {
        clearos_profile(__METHOD__, __LINE__);

        $state = new File(self::FILE_STATE);
        try {
            if ($state->exists()) $state->delete();
            $state->create('webconfig', 'webconfig', '0644');
        }
        catch (Exception $e) {
            Logger::syslog(self::TAG, "Error: {$e->getMessage()}");
            return 1;
        }

        $samples = array();

        while (true) {
            $samples[time()] = $this->collect_sample();

            if (count($samples) > self::MAX_SAMPLES) {
                $samples = array_reverse($samples, TRUE);
                array_pop($samples);
            }

            try {
                ksort($samples);
                $state->replace_contents_locked(
                    json_encode($samples));
            }
            catch (Exception $e) {
                Logger::syslog(self::TAG, "Error {$e->getMessage()}");
                return 1;
            }

            sleep(self::INTERVAL_UPDATE);
        }

        return 0;
    }

    /**
     * Return data samples (> $last_sample).
     *
     */

    final static public function get_samples($last_sample)
    {
        clearos_profile(__METHOD__, __LINE__);

        $history = array();
        try {
            $state = new File(self::FILE_STATE);
            if ($state->exists())
                $history = json_decode($state->get_contents_locked());
        }
        catch (Exception $e) { }

        $samples = null;
        foreach ($history as $timestamp => $sample) {
            if ($timestamp <= $last_sample) continue;
            $samples[$timestamp] = $sample;
        }

        return $samples;
    }

    /**
     * Collect data samples for bandwidth usage, load averaes, and memory.
     *
     */

    final private function collect_sample()
    {
        $timestamp = time();
        $iface_manager = new Iface_Manager;

        $bw_up = gmp_init(0);
        $bw_down = gmp_init(0);

        $externals = $iface_manager->get_external_interfaces();
        foreach ($externals as $extif) {
            try {
                $file = new File(
                    sprintf(self::FORMAT_NET_TX_BYTES, $extif));
                $bw_up = gmp_add($bw_up, trim($file->get_contents()));

                $file = new File(
                    sprintf(self::FORMAT_NET_RX_BYTES, $extif));
                $bw_down = gmp_add($bw_down, trim($file->get_contents()));
            }
            catch (File_Not_Found_Exception $e) {
                continue;
            }
        }

        $data['bandwidth_up'] = sprintf(
            '%s.%s',
            gmp_strval(gmp_div_q($bw_up, 1024)),
            gmp_strval(gmp_div_r($bw_up, 1024))
        );
        $data['bandwidth_down'] = sprintf(
            '%s.%s',
            gmp_strval(gmp_div_q($bw_down, 1024)),
            gmp_strval(gmp_div_r($bw_down, 1024))
        );

        $data['loadavg_5min'] = 0.0;
        $data['loadavg_15min'] = 0.0;

        $file = new File(self::FILE_PROC_LOADAVG);
        $loadavg = $file->get_contents_as_array();

        foreach ($loadavg as $line) {
            if (!preg_match(
                '/^\d+\.\d+\s+(\d+\.\d+)\s+(\d+\.\d+)/',
                $line, $matches)) continue;
            $data['loadavg_5min'] = $matches[1];
            $data['loadavg_15min'] = $matches[2];
            break;
        }

        $data['mem_active'] = 0.0;
        $data['mem_swap_total'] = 0.0;
        $data['mem_swap_free'] = 0.0;
        $data['mem_swap_used'] = 0.0;

        $file = new File(self::FILE_PROC_MEMINFO);
        $meminfo = $file->get_contents_as_array();

        foreach ($meminfo as $line) {
            if (preg_match(
                '/^Active:\s*(\d+)/', $line, $matches)) {
                $data['mem_active'] = (float)$matches[1] / 1024.0;
            }
            else if (preg_match(
                '/^SwapTotal:\s*(\d+)/', $line, $matches)) {
                $data['mem_swap_total'] = (float)$matches[1] / 1024.0;
            }
            else if (preg_match(
                '/^SwapFree:\s*(\d+)/', $line, $matches)) {
                $data['mem_swap_free'] = (float)$matches[1] / 1024.0;
            }
        }

        $data['mem_swap_used'] =
            $data['mem_swap_total'] - $data['mem_swap_free'];

        return $data;
    }
}

