<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'mobile_demo';
$app['version'] = '1.2.5';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['summary'] = lang('mobile_demo_mobile_demo');
$app['description'] = lang('mobile_demo_description');
$app['tooltip'] = lang('mobile_demo_tooltip');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('mobile_demo_mobile_demo');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('mobile_demo_subcategory');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_requires'] = array(
    'app-base-core >= 1.4.7', 
    'app-network-core', 
);

$app['core_directory_manifest'] = array(
   '/var/state/webconfig' => array('mode' => '750', 'owner' => 'root', 'group' => 'webconfig')
);

$app['core_file_manifest'] = array(
    'mobile_demo.acl' => array(
        'target' => '/var/clearos/base/access_control/public/mobile_demo'
    ),
    'mobile-demo.php' => array(
        'target' => '/var/clearos/base/daemon/mobile-demo.php'
    ),
);

$app['services'] = array(
    'mobile-demo' => array(
        'class' => 'Mobile_Demo_Service',
        'name' => lang('mobile_demo_mobile_demo'),
        'description' => lang('mobile_demo_description'),
    ),
);

$app['delete_dependency'] = array(
    'app-mobile-demo-core'
);

