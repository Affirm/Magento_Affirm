<?php

define("DS", "/");

$vendorDir = dirname(__FILE__) . DS . '..' . DS . 'vendor';

$ok = include_once( $vendorDir . DS . 'autoload.php');

if (!$ok) {
    echo "MISSING DEPENDENCIES: Execute composer or see README.\n";
    exit(1);
}

$ok = @include_once(
    $vendorDir . DS . 'simpletest' . DS . 'simpletest' . DS . 'autorun.php' );

if (!$ok) {
    echo "MISSING DEPENDENCY: The Affirm API test cases depend on SimpleTest. ".
        "Download it at <http://www.simpletest.org/>, and either install it ".
        "in your PHP include_path or put it in the test/ directory.\n";
    exit(1);
}

require_once(dirname(__FILE__) . '/../extension/lib/Affirm/Affirm.php');

require_once(dirname(__FILE__) . "/Affirm/UtilTest.php");
