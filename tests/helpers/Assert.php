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

function assertDatesEquals($message, $expected, $actual, $toleranceSeconds = 0) {
	$diff = abs(strtotime($expected) - strtotime($actual));
	$msg = $message . " [". $expected . "]vs[" . $actual ."]==";
	if ($diff > $toleranceSeconds) {
		$msg .= "FAILED";
		Debugger::error($msg);
		exit(1);
	}
	$msg .= "PASSED";
	Debugger::info($msg);
}

?>

