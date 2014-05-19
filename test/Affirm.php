<?php

$testURI = '/simpletest/autorun.php';
$ok = @include_once(dirname(__FILE__).$testURI);
if (!$ok) {
    $ok = @include_once(dirname(__FILE__).'/../vendor/simpletest'.$testURI);
}
if (!$ok) {
    echo "MISSING DEPENDENCY: The Affirm API test cases depend on SimpleTest. ".
        "Download it at <http://www.simpletest.org/>, and either install it ".
        "in your PHP include_path or put it in the test/ directory.\n";
    exit(1);
}

require_once(dirname(__FILE__) . '/../extension/lib/Affirm/Affirm.php');

require_once(dirname(__FILE__) . "/Affirm/UtilTest.php");
