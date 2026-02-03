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
 * billing class provides prepaid and digit-based billing functionality
 *
 * @method null calculate_cost
 */
if (!class_exists('billing')) {
	class billing {

		/**
		 * Database object
		 */
		public $database;

		/**
		 * Domain UUID
		 */
		public $domain_uuid;

		/**
		 * Extension UUID
		 */
		public $extension_uuid;

		/**
		 * Debug mode
		 */
		public $debug = false;

		/**
		 * Called class constructor
		 */
		public function __construct() {
			//connect to the database if not connected
			if (!isset($this->database)) {
				require_once "resources/classes/database.php";
				$database = new database;
				$this->database = $database->connect();
			}
		}

		/**
		 * Find the best matching rate for a destination number
		 * Supports multiple prefixes per user via v_billing_user_rates
		 *
		 * @param string $destination_number the destination number to match
		 * @return array|null the matching rate details or null if no match
		 */
		public function find_rate($destination_number) {
			//validate input
			if (empty($destination_number)) {
				return null;
			}
			if (empty($this->domain_uuid)) {
				return null;
			}
			if (empty($this->extension_uuid)) {
				return null;
			}

			//remove non-numeric characters
			$destination_number = preg_replace('/[^0-9]/', '', $destination_number);

			//find the best matching rate (longest prefix match)
			//join with v_billing_user_rates to support multiple prefixes per user
			$sql = "SELECT r.billing_rate_uuid, r.rate_name, r.destination_prefix, ";
			$sql .= "r.rate_per_minute, r.billing_increment, r.minimum_duration, ";
			$sql .= "r.connection_fee, r.currency ";
			$sql .= "FROM v_billing_rates r ";
			$sql .= "INNER JOIN v_billing_user_rates ur ON r.billing_rate_uuid = ur.billing_rate_uuid ";
			$sql .= "WHERE r.domain_uuid = :domain_uuid ";
			$sql .= "AND ur.extension_uuid = :extension_uuid ";
			$sql .= "AND r.enabled = true ";
			$sql .= "AND ur.enabled = true ";
			$sql .= "AND :destination_number LIKE r.destination_prefix || '%' ";
			$sql .= "ORDER BY LENGTH(r.destination_prefix) DESC ";
			$sql .= "LIMIT 1";

			$parameters['domain_uuid'] = $this->domain_uuid;
			$parameters['extension_uuid'] = $this->extension_uuid;
			$parameters['destination_number'] = $destination_number;

			$result = $this->database->select($sql, $parameters, 'row');

			if ($this->debug) {
				echo "SQL: " . $sql . "\n";
				echo "Destination: " . $destination_number . "\n";
				print_r($result);
			}

			return $result;
		}

		/**
		 * Calculate the cost of a call based on duration and rate
		 *
		 * @param int $duration call duration in seconds
		 * @param array $rate rate details from find_rate()
		 * @return array cost details including billable_duration, rate_applied, connection_fee, and total_cost
		 */
		public function calculate_cost($duration, $rate) {
			//validate input
			if (empty($duration) || $duration <= 0) {
				return array(
					'billable_duration' => 0,
					'rate_applied' => 0,
					'connection_fee' => 0,
					'total_cost' => 0
				);
			}
			if (empty($rate)) {
				return null;
			}

			//get rate parameters
			$rate_per_minute = floatval($rate['rate_per_minute']);
			$billing_increment = intval($rate['billing_increment']) ?: 60;
			$minimum_duration = intval($rate['minimum_duration']) ?: 0;
			$connection_fee = floatval($rate['connection_fee']) ?: 0;

			//apply minimum duration
			$billable_duration = max($duration, $minimum_duration);

			//round up to next billing increment
			if ($billing_increment > 0) {
				$billable_duration = ceil($billable_duration / $billing_increment) * $billing_increment;
			}

			//calculate cost: (duration / 60) * rate_per_minute + connection_fee
			$call_cost = ($billable_duration / 60.0) * $rate_per_minute;
			$total_cost = $call_cost + $connection_fee;

			if ($this->debug) {
				echo "Duration: {$duration}s\n";
				echo "Minimum Duration: {$minimum_duration}s\n";
				echo "Billing Increment: {$billing_increment}s\n";
				echo "Billable Duration: {$billable_duration}s\n";
				echo "Rate per Minute: {$rate_per_minute}\n";
				echo "Connection Fee: {$connection_fee}\n";
				echo "Total Cost: {$total_cost}\n";
			}

			return array(
				'billable_duration' => $billable_duration,
				'rate_applied' => $rate_per_minute,
				'connection_fee' => $connection_fee,
				'call_cost' => $call_cost,
				'total_cost' => $total_cost,
				'currency' => $rate['currency']
			);
		}

		/**
		 * Get the current balance for an extension
		 *
		 * @return float|null current balance or null if not found
		 */
		public function get_balance() {
			//validate input
			if (empty($this->domain_uuid)) {
				return null;
			}
			if (empty($this->extension_uuid)) {
				return null;
			}

			$sql = "SELECT balance, currency FROM v_billing_balances ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND extension_uuid = :extension_uuid ";

			$parameters['domain_uuid'] = $this->domain_uuid;
			$parameters['extension_uuid'] = $this->extension_uuid;

			$result = $this->database->select($sql, $parameters, 'row');

			if ($result) {
				return floatval($result['balance']);
			}

			return null;
		}

		/**
		 * Update the balance for an extension
		 *
		 * @param float $amount amount to add (positive) or deduct (negative)
		 * @return bool success status
		 */
		public function update_balance($amount) {
			//validate input
			if (empty($this->domain_uuid)) {
				return false;
			}
			if (empty($this->extension_uuid)) {
				return false;
			}

			//get current balance
			$current_balance = $this->get_balance();
			if ($current_balance === null) {
				return false;
			}

			//calculate new balance
			$new_balance = $current_balance + $amount;

			//update balance
			$sql = "UPDATE v_billing_balances SET ";
			$sql .= "balance = :balance, ";
			$sql .= "last_updated = NOW() ";
			$sql .= "WHERE domain_uuid = :domain_uuid ";
			$sql .= "AND extension_uuid = :extension_uuid ";

			$parameters['balance'] = $new_balance;
			$parameters['domain_uuid'] = $this->domain_uuid;
			$parameters['extension_uuid'] = $this->extension_uuid;

			$this->database->execute($sql, $parameters);

			if ($this->debug) {
				echo "Balance updated: {$current_balance} + {$amount} = {$new_balance}\n";
			}

			return true;
		}

		/**
		 * Check if there is sufficient balance for a call
		 *
		 * @param float $required_amount amount required for the call
		 * @return bool true if sufficient balance, false otherwise
		 */
		public function check_balance($required_amount) {
			$current_balance = $this->get_balance();
			if ($current_balance === null) {
				return false;
			}
			return ($current_balance >= $required_amount);
		}

		/**
		 * Process billing for a completed call from CDR
		 *
		 * @param string $xml_cdr_uuid UUID of the CDR record
		 * @param string $destination_number destination number
		 * @param int $duration call duration in seconds
		 * @param string $call_date call date/time
		 * @return bool success status
		 */
		public function process_call($xml_cdr_uuid, $destination_number, $duration, $call_date = null) {
			//validate input
			if (empty($xml_cdr_uuid) || empty($destination_number) || empty($duration)) {
				return false;
			}
			if (empty($this->domain_uuid) || empty($this->extension_uuid)) {
				return false;
			}

			//find matching rate
			$rate = $this->find_rate($destination_number);
			if (!$rate) {
				if ($this->debug) {
					echo "No rate found for destination: {$destination_number}\n";
				}
				return false;
			}

			//calculate cost
			$cost_details = $this->calculate_cost($duration, $rate);
			if (!$cost_details) {
				return false;
			}

			//deduct from balance
			$this->update_balance(-$cost_details['total_cost']);

			//save usage record
			$billing_usage_uuid = uuid();
			$sql = "INSERT INTO v_billing_usage (";
			$sql .= "billing_usage_uuid, domain_uuid, extension_uuid, xml_cdr_uuid, ";
			$sql .= "billing_rate_uuid, destination_number, matched_prefix, ";
			$sql .= "duration, billable_duration, rate_applied, connection_fee, ";
			$sql .= "cost, currency, call_date, insert_date";
			$sql .= ") VALUES (";
			$sql .= ":billing_usage_uuid, :domain_uuid, :extension_uuid, :xml_cdr_uuid, ";
			$sql .= ":billing_rate_uuid, :destination_number, :matched_prefix, ";
			$sql .= ":duration, :billable_duration, :rate_applied, :connection_fee, ";
			$sql .= ":cost, :currency, :call_date, NOW()";
			$sql .= ")";

			$parameters['billing_usage_uuid'] = $billing_usage_uuid;
			$parameters['domain_uuid'] = $this->domain_uuid;
			$parameters['extension_uuid'] = $this->extension_uuid;
			$parameters['xml_cdr_uuid'] = $xml_cdr_uuid;
			$parameters['billing_rate_uuid'] = $rate['billing_rate_uuid'];
			$parameters['destination_number'] = $destination_number;
			$parameters['matched_prefix'] = $rate['destination_prefix'];
			$parameters['duration'] = $duration;
			$parameters['billable_duration'] = $cost_details['billable_duration'];
			$parameters['rate_applied'] = $cost_details['rate_applied'];
			$parameters['connection_fee'] = $cost_details['connection_fee'];
			$parameters['cost'] = $cost_details['total_cost'];
			$parameters['currency'] = $cost_details['currency'];
			$parameters['call_date'] = $call_date ?: date('Y-m-d H:i:s');

			$this->database->execute($sql, $parameters);

			if ($this->debug) {
				echo "Usage record created: {$billing_usage_uuid}\n";
				echo "Cost deducted: {$cost_details['total_cost']}\n";
			}

			return true;
		}

	}
}

?>
