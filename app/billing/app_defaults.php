<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2025
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

	if ($domains_processed == 1) {
		//check if mod_nibblebill config exists, if not create it
		$nibblebill_conf = $settings->get('switch', 'conf')."/autoload_configs/nibblebill.conf.xml";
		
		if (!file_exists($nibblebill_conf)) {
			$nibblebill_config = '<?xml version="1.0" encoding="utf-8"?>
<configuration name="nibblebill.conf" description="Nibblebill Configuration">
  <settings>
    <param name="db_table" value="v_billing_balances"/>
    <param name="db_column_cash" value="balance"/>
    <param name="db_column_account" value="extension_uuid"/>
    <param name="custom_sql_lookup" value="SELECT balance FROM v_billing_balances WHERE extension_uuid=\'${nibble_account}\' AND domain_uuid=\'${domain_uuid}\'"/>
    <param name="custom_sql_save" value="UPDATE v_billing_balances SET balance=${nibble_balance}, last_updated=NOW() WHERE extension_uuid=\'${nibble_account}\' AND domain_uuid=\'${domain_uuid}\'"/>
    <param name="heartbeat_update_rate" value="60"/>
    <param name="lowbal_amt" value="5"/>
    <param name="nobal_amt" value="0"/>
    <param name="percall_action" value="hangup"/>
    <param name="percall_max_amt" value="1000"/>
  </settings>
</configuration>';
			
			$fout = fopen($nibblebill_conf, "w");
			if ($fout) {
				fwrite($fout, $nibblebill_config);
				fclose($fout);
				if ($display_type == "text") {
					echo "	nibblebill.conf.xml: 	created\n";
				}
			}
		}
	}

?>
