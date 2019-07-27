<?php

function assertEquals($message, $expected, $actual)
{
    $msg = $message . " [" . $expected . "]vs[" . $actual . "]==";
    if ($expected != $actual) {
        $msg .= "FAILED";
        Debugger::error($msg);
        exit(1);
    }
    $msg .= "PASSED";
    Debugger::info($msg);
}

?>

