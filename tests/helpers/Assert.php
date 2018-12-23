<?php

function assertEquals($message, $expected, $actual) {
	print($message."[".$expected."]v[".$actual."]::");
	if ($expected != $actual) {
		print("FAILED\n");
		exit (1);
	}
	print("PASSED\n");
}

?>

