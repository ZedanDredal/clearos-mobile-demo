<?php

/**
 * Mobile Demo view.
 *
 * @category   apps
 * @package    mobile-demo
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/devel/
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

$this->lang->load('base');
$this->lang->load('mobile_demo');

?>
<h1><? echo lang('mobile_demo_mobile_demo'); ?></h1>
<p>This application demonstrates how to access the ClearOS API framework from a mobile phone using a REST-like protocol.  The bundled application is for Android™ devices, but could be easily adapted to support iOS™ platforms.</p>

<h1><? echo lang('mobile_demo_download'); ?></h1>
<p>To get started, install the following application from Google Play to your Android™ device.</p>
<?php echo anchor_custom(
    'http://play.google.com/store/apps/details?id=com.clearcenter.mobile_demo',
    lang('mobile_demo_download_install')); ?>

<h1><? echo lang('mobile_demo_configuration'); ?></h1>
<p>Launch the application and tap <strong>Add New Account</strong>.  The ClearOS Accounts Settings dialog will appear which has four fields to populate (see the first screen shot below):</p>
<ul>
<li><strong>Nickname</strong> This must be a unique name (in the event you add multiple accounts) which easily identifies your ClearOS server.</li>
<li><strong>Hostname</strong> Enter the hostname or IP address of your server here.  Since the ClearOS Webconfig service runs on a non-standard port (usually port 81), you may have to append <b>:81</b> to the hostname, for example: <b>myserver.poweredbyclear.com:81</b>.</li>
<li><strong>Username</strong> Specify the username you would like to use.  You do not have to use the <b>root</b> account, but it's safe to do so as the password for whichever account you use will never be saved to your device.</li>
<li><strong>Password</strong> Supply the corresponding password here.  As mentioned above, your password will never be saved on the device.  The password is used to obtain a session cookie.  When that cookie expires, you will see a notification icon indicating that you must reauthenticate.</li>
</ul>
<p>After you add an account it may take up to 10 minutes to start seeing data on the status screen.  A broadcast occurs when an account is added/updated/removed which causes a lot of other services to start checking in.  The Mobile Demo may have to wait in a queue to run.  Apparently this is normal, please be patient.</p>
<p>Currently, the default Webconfig session time-out is set to 2 hours (7200 seconds).  You will be notified by a status icon when a session expires.  I have tried to extend this time-out dynamically without success.  More to come on how one can extend the session time-out in Webconfig/CodeIgniter.</p>
<p>To remove an account, long-press over the system name from the main dialog, or navigate to <strong>System Settings > Accounts & sync</strong> and remove the Mobile Demo account the usual way.</p>

<h1><? echo lang('mobile_demo_source_code'); ?></h1>
<p>The source code for both the Android™ and Webconfig applications is publically available from the ClearFoundation <a href="http://www.clearfoundation.com/docs/developer/source_code/">SVN server</a>.</p>
<ul>
<li>Webconfig: svn://scm.clearfoundation.com/clearos/webconfig/apps/mobile_demo</li>
<li>Android™: svn://scm.clearfoundation.com/clearos/packages/clearos-mobile-demo</li>
</ul>

<h1><? echo lang('mobile_demo_screen_shots'); ?></h1>
<table border='0'><tr>
<td><img width='240' height='400' src='<?php echo clearos_app_htdocs('mobile_demo') . '/screenshot1.png'; ?>' /><br>Login dialog.</td>
<td><img width='240' height='400' src='<?php echo clearos_app_htdocs('mobile_demo') . '/screenshot2.png'; ?>' /><br>Example status screen.</td>
</tr></table>

