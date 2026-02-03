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
 * Nibblebill API - Balance Query Endpoint for mod_nibblebill
 * 
 * This endpoint is called by FreeSWITCH mod_nibblebill to check and update balances
 * 
 * Expected parameters:
 * - action: check_balance, deduct_balance
 * - account: extension_uuid or extension number
 * - domain_uuid: domain UUID
 * - amount: amount to deduct (for deduct_balance action)
 * - call_uuid: UUID of the active call (optional)
 */

//includes
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/classes/billing.php";

//set content type
	header('Content-Type: application/json');

//get parameters
	$action = $_REQUEST['action'] ?? 'check_balance';
	$account = $_REQUEST['account'] ?? '';
	$domain_uuid = $_REQUEST['domain_uuid'] ?? '';
	$amount = floatval($_REQUEST['amount'] ?? 0);
	$call_uuid = $_REQUEST['call_uuid'] ?? '';
	$destination = $_REQUEST['destination'] ?? '';

//validate required parameters
	if (empty($account) || empty($domain_uuid)) {
		echo json_encode(array(
			'success' => false,
			'error' => 'Missing required parameters: account, domain_uuid',
			'balance' => 0
		));
		exit;
	}

//get database connection
	$database = new database;

//resolve account to extension_uuid if needed
	$extension_uuid = null;
	if (is_uuid($account)) {
		$extension_uuid = $account;
	}
	else {
		//look up by extension number
		$sql = "SELECT extension_uuid FROM v_extensions ";
		$sql .= "WHERE domain_uuid = :domain_uuid ";
		$sql .= "AND (extension = :account OR number_alias = :account) ";
		$sql .= "LIMIT 1";
		
		$parameters['domain_uuid'] = $domain_uuid;
		$parameters['account'] = $account;
		
		$result = $database->select($sql, $parameters, 'row');
		if ($result) {
			$extension_uuid = $result['extension_uuid'];
		}
	}

//validate extension found
	if (!$extension_uuid) {
		echo json_encode(array(
			'success' => false,
			'error' => 'Account not found',
			'balance' => 0
		));
		exit;
	}

//initialize billing class
	$billing = new billing;
	$billing->database = $database;
	$billing->domain_uuid = $domain_uuid;
	$billing->extension_uuid = $extension_uuid;

//handle action
	switch ($action) {
		case 'check_balance':
			//get current balance
			$balance = $billing->get_balance();
			
			if ($balance === null) {
				echo json_encode(array(
					'success' => false,
					'error' => 'No balance record found for this account',
					'balance' => 0
				));
			}
			else {
				echo json_encode(array(
					'success' => true,
					'balance' => floatval($balance),
					'account' => $account,
					'extension_uuid' => $extension_uuid
				));
			}
			break;
			
		case 'deduct_balance':
			//validate amount
			if ($amount <= 0) {
				echo json_encode(array(
					'success' => false,
					'error' => 'Invalid amount',
					'balance' => 0
				));
				exit;
			}
			
			//check sufficient balance
			if (!$billing->check_balance($amount)) {
				echo json_encode(array(
					'success' => false,
					'error' => 'Insufficient balance',
					'balance' => $billing->get_balance()
				));
				exit;
			}
			
			//deduct balance
			$description = "Real-time deduction during call";
			if (!empty($destination)) {
				$description .= " to " . $destination;
			}
			if (!empty($call_uuid)) {
				$description .= " (Call UUID: " . $call_uuid . ")";
			}
			
			$result = $billing->update_balance(-$amount, 'call_cost', $description);
			
			if ($result) {
				$new_balance = $billing->get_balance();
				echo json_encode(array(
					'success' => true,
					'balance' => floatval($new_balance),
					'amount_deducted' => $amount,
					'account' => $account
				));
			}
			else {
				echo json_encode(array(
					'success' => false,
					'error' => 'Failed to deduct balance',
					'balance' => $billing->get_balance()
				));
			}
			break;
			
		case 'check_rate':
			//find rate for destination
			if (empty($destination)) {
				echo json_encode(array(
					'success' => false,
					'error' => 'Destination required for rate check'
				));
				exit;
			}
			
			$rate = $billing->find_rate($destination);
			
			if ($rate) {
				echo json_encode(array(
					'success' => true,
					'rate_per_minute' => floatval($rate['rate_per_minute']),
					'billing_increment' => intval($rate['billing_increment']),
					'connection_fee' => floatval($rate['connection_fee']),
					'currency' => $rate['currency'],
					'matched_prefix' => $rate['destination_prefix']
				));
			}
			else {
				echo json_encode(array(
					'success' => false,
					'error' => 'No rate found for destination'
				));
			}
			break;
			
		case 'calculate_cost':
			//calculate cost for a call
			$duration = intval($_REQUEST['duration'] ?? 0);
			
			if ($duration <= 0 || empty($destination)) {
				echo json_encode(array(
					'success' => false,
					'error' => 'Duration and destination required'
				));
				exit;
			}
			
			$rate = $billing->find_rate($destination);
			
			if (!$rate) {
				echo json_encode(array(
					'success' => false,
					'error' => 'No rate found for destination'
				));
				exit;
			}
			
			$cost = $billing->calculate_cost($duration, $rate);
			
			echo json_encode(array(
				'success' => true,
				'duration' => $duration,
				'billable_duration' => $cost['billable_duration'],
				'rate_per_minute' => $cost['rate_applied'],
				'connection_fee' => $cost['connection_fee'],
				'total_cost' => $cost['total_cost'],
				'currency' => $cost['currency']
			));
			break;
			
		default:
			echo json_encode(array(
				'success' => false,
				'error' => 'Unknown action: ' . $action
			));
	}

?>
