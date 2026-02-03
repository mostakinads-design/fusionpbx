<?php

	$y=0;
	$apps[$x]['menu'][$y]['title']['en-us'] = "Billing";
	$apps[$x]['menu'][$y]['title']['en-gb'] = "Billing";
	$apps[$x]['menu'][$y]['uuid'] = "b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f";
	$apps[$x]['menu'][$y]['parent_uuid'] = "fd29e39c-c936-f5fc-8e2b-611681b266b5";
	$apps[$x]['menu'][$y]['category'] = "internal";
	$apps[$x]['menu'][$y]['path'] = "/app/billing/billing_rates.php";
	$apps[$x]['menu'][$y]['order'] = "";
	$apps[$x]['menu'][$y]['groups'][] = "superadmin";
	$apps[$x]['menu'][$y]['groups'][] = "admin";

	$y++;
	$apps[$x]['menu'][$y]['title']['en-us'] = "Rate Plans";
	$apps[$x]['menu'][$y]['title']['en-gb'] = "Rate Plans";
	$apps[$x]['menu'][$y]['uuid'] = "c23d0b9f-6f5e-5c4b-9g3f-2b3c4d5e6f7g";
	$apps[$x]['menu'][$y]['parent_uuid'] = "b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f";
	$apps[$x]['menu'][$y]['category'] = "internal";
	$apps[$x]['menu'][$y]['path'] = "/app/billing/billing_rates.php";
	$apps[$x]['menu'][$y]['order'] = "1";
	$apps[$x]['menu'][$y]['groups'][] = "superadmin";
	$apps[$x]['menu'][$y]['groups'][] = "admin";

	$y++;
	$apps[$x]['menu'][$y]['title']['en-us'] = "Prepaid Balances";
	$apps[$x]['menu'][$y]['title']['en-gb'] = "Prepaid Balances";
	$apps[$x]['menu'][$y]['uuid'] = "d34e1c0g-7g6f-6d5c-0h4g-3c4d5e6f7g8h";
	$apps[$x]['menu'][$y]['parent_uuid'] = "b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f";
	$apps[$x]['menu'][$y]['category'] = "internal";
	$apps[$x]['menu'][$y]['path'] = "/app/billing/billing_balances.php";
	$apps[$x]['menu'][$y]['order'] = "2";
	$apps[$x]['menu'][$y]['groups'][] = "superadmin";
	$apps[$x]['menu'][$y]['groups'][] = "admin";

	$y++;
	$apps[$x]['menu'][$y]['title']['en-us'] = "Usage History";
	$apps[$x]['menu'][$y]['title']['en-gb'] = "Usage History";
	$apps[$x]['menu'][$y]['uuid'] = "e45f2d1h-8h7g-7e6d-1i5h-4d5e6f7g8h9i";
	$apps[$x]['menu'][$y]['parent_uuid'] = "b12c9a8f-5e4d-4b3a-8f2e-1a2b3c4d5e6f";
	$apps[$x]['menu'][$y]['category'] = "internal";
	$apps[$x]['menu'][$y]['path'] = "/app/billing/billing_usage.php";
	$apps[$x]['menu'][$y]['order'] = "3";
	$apps[$x]['menu'][$y]['groups'][] = "superadmin";
	$apps[$x]['menu'][$y]['groups'][] = "admin";
	$apps[$x]['menu'][$y]['groups'][] = "user";

?>
