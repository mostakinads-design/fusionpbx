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

/**
 * CDR Billing Hook - automatically process billing when calls complete
 * 
 * This file should be called from XML CDR processing
 * Can be integrated via event socket or called directly after CDR insert
 */

//includes
	require_once dirname(__DIR__, 4) . "/resources/require.php";
	require_once dirname(__DIR__, 1) . "/classes/billing.php";

/**
 * Process billing for a CDR record
 * 
 * @param string $xml_cdr_uuid UUID of the CDR record
 * @param string $domain_uuid Domain UUID
 * @return bool success status
 */
function process_cdr_billing($xml_cdr_uuid, $domain_uuid) {
	//validate input
	if (empty($xml_cdr_uuid) || empty($domain_uuid)) {
		return false;
	}

	//get database connection
	$database = new database;
	
	//get CDR details
	$sql = "SELECT xml_cdr_uuid, domain_uuid, extension_uuid, ";
	$sql .= "destination_number, billsec as duration, start_stamp ";
	$sql .= "FROM v_xml_cdr ";
	$sql .= "WHERE xml_cdr_uuid = :xml_cdr_uuid ";
	$sql .= "AND domain_uuid = :domain_uuid ";
	$sql .= "AND billsec > 0 "; // only process answered calls
	
	$parameters['xml_cdr_uuid'] = $xml_cdr_uuid;
	$parameters['domain_uuid'] = $domain_uuid;
	
	$cdr = $database->select($sql, $parameters, 'row');
	
	if (!$cdr) {
		return false; // CDR not found or not billable
	}
	
	//check if already billed
	$sql_check = "SELECT billing_usage_uuid FROM v_billing_usage ";
	$sql_check .= "WHERE xml_cdr_uuid = :xml_cdr_uuid ";
	$params_check['xml_cdr_uuid'] = $xml_cdr_uuid;
	$already_billed = $database->select($sql_check, $params_check, 'row');
	
	if ($already_billed) {
		return false; // already processed
	}
	
	//initialize billing class
	$billing = new billing;
	$billing->database = $database;
	$billing->domain_uuid = $cdr['domain_uuid'];
	$billing->extension_uuid = $cdr['extension_uuid'];
	
	//process the call billing
	$result = $billing->process_call(
		$cdr['xml_cdr_uuid'],
		$cdr['destination_number'],
		$cdr['duration'],
		$cdr['start_stamp']
	);
	
	return $result;
}

/**
 * Batch process billing for all unbilled CDRs
 * Can be run from cron or manually
 * 
 * @param string $domain_uuid Optional domain UUID to limit processing
 * @return array statistics of processed records
 */
function batch_process_billing($domain_uuid = null) {
	$stats = array(
		'processed' => 0,
		'failed' => 0,
		'total_cost' => 0
	);
	
	//get database connection
	$database = new database;
	
	//find unbilled CDRs
	$sql = "SELECT c.xml_cdr_uuid, c.domain_uuid ";
	$sql .= "FROM v_xml_cdr c ";
	$sql .= "LEFT JOIN v_billing_usage u ON c.xml_cdr_uuid = u.xml_cdr_uuid ";
	$sql .= "WHERE u.billing_usage_uuid IS NULL ";
	$sql .= "AND c.billsec > 0 ";
	$sql .= "AND c.direction = 'outbound' ";
	
	$parameters = array();
	if (!empty($domain_uuid)) {
		$sql .= "AND c.domain_uuid = :domain_uuid ";
		$parameters['domain_uuid'] = $domain_uuid;
	}
	
	$sql .= "ORDER BY c.start_stamp DESC ";
	$sql .= "LIMIT 1000"; // process in batches
	
	$unbilled_cdrs = $database->select($sql, $parameters, 'all');
	
	if (is_array($unbilled_cdrs)) {
		foreach ($unbilled_cdrs as $cdr) {
			$result = process_cdr_billing($cdr['xml_cdr_uuid'], $cdr['domain_uuid']);
			if ($result) {
				$stats['processed']++;
			}
			else {
				$stats['failed']++;
			}
		}
	}
	
	return $stats;
}

?>
