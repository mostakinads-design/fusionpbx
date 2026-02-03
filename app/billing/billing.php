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
	Portions created by the Initial Developer are Copyright (C) 2008-2026
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/

//includes files
	require_once dirname(__DIR__, 2) . "/resources/require.php";
	require_once "resources/check_auth.php";

//check permissions
	if (!permission_exists('billing_view')) {
		echo "access denied";
		exit;
	}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//get statistics
	$sql = "select count(*) as count from v_billing_rates where domain_uuid = :domain_uuid or domain_uuid is null";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$rate_count = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);

	$sql = "select count(*) as count from v_billing_balances where domain_uuid = :domain_uuid";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$balance_count = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);

	$sql = "select count(*) as count from v_billing_usage where domain_uuid = :domain_uuid";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$usage_count = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);

	//get low balance count
	$sql = "select count(*) as count from v_billing_balances ";
	$sql .= "where domain_uuid = :domain_uuid ";
	$sql .= "and low_balance_alert = 'true' ";
	$sql .= "and balance <= low_balance_threshold ";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$low_balance_count = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);

	//get total balance
	$sql = "select sum(balance) as total from v_billing_balances where domain_uuid = :domain_uuid";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$total_balance = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);
	$total_balance = $total_balance ?: 0;

	//get today's usage
	$sql = "select sum(cost) as total from v_billing_usage ";
	$sql .= "where domain_uuid = :domain_uuid ";
	$sql .= "and date(call_date) = current_date ";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$today_usage = $database->select($sql, $parameters, 'column');
	unset($sql, $parameters);
	$today_usage = $today_usage ?: 0;

//show the header
	$document['title'] = $text['title-billing'];
	require_once "resources/header.php";

//show the content
	echo "<div class='action_bar' id='action_bar'>\n";
	echo "	<div class='heading'><b>".$text['title-billing']."</b></div>\n";
	echo "	<div class='actions'>\n";
	echo button::create(['type'=>'button','label'=>$text['button-refresh'],'icon'=>$_SESSION['theme']['button_icon_reload'],'onclick'=>'location.reload();']);
	echo "	</div>\n";
	echo "	<div style='clear: both;'></div>\n";
	echo "</div>\n";

	echo "<div class='container-fluid' style='padding: 25px 0 0 0;'>\n";
	echo "	<div class='row'>\n";

	//Rate Plans Card
	echo "		<div class='col-sm-6 col-md-4 col-lg-3'>\n";
	echo "			<div class='card text-center' style='min-height: 150px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-rate_plans']."</h5>\n";
	echo "					<h1 class='card-text'>".escape($rate_count)."</h1>\n";
	echo "					<a href='billing_rates.php' class='btn btn-primary'>".$text['button-view']."</a>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	//Prepaid Balances Card
	echo "		<div class='col-sm-6 col-md-4 col-lg-3'>\n";
	echo "			<div class='card text-center' style='min-height: 150px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-prepaid_balances']."</h5>\n";
	echo "					<h1 class='card-text'>".escape($balance_count)."</h1>\n";
	echo "					<a href='billing_balances.php' class='btn btn-primary'>".$text['button-view']."</a>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	//Usage History Card
	echo "		<div class='col-sm-6 col-md-4 col-lg-3'>\n";
	echo "			<div class='card text-center' style='min-height: 150px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-usage_records']."</h5>\n";
	echo "					<h1 class='card-text'>".escape($usage_count)."</h1>\n";
	echo "					<a href='billing_usage.php' class='btn btn-primary'>".$text['button-view']."</a>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	//Low Balance Alerts Card
	echo "		<div class='col-sm-6 col-md-4 col-lg-3'>\n";
	$alert_class = $low_balance_count > 0 ? 'border-danger' : '';
	echo "			<div class='card text-center {$alert_class}' style='min-height: 150px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-low_balance_alerts']."</h5>\n";
	echo "					<h1 class='card-text text-".($low_balance_count > 0 ? 'danger' : 'success')."'>".escape($low_balance_count)."</h1>\n";
	echo "					<a href='billing_balances.php' class='btn btn-".($low_balance_count > 0 ? 'danger' : 'secondary')."'>".$text['button-view']."</a>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	echo "	</div>\n"; //row
	echo "	<br>\n";
	echo "	<div class='row'>\n";

	//Total Balance Card
	echo "		<div class='col-sm-6 col-md-6'>\n";
	echo "			<div class='card' style='min-height: 120px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-total_balance']."</h5>\n";
	echo "					<h2 class='card-text'>$".number_format($total_balance, 2)."</h2>\n";
	echo "					<small class='text-muted'>".$text['description-total_balance']."</small>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	//Today's Usage Card
	echo "		<div class='col-sm-6 col-md-6'>\n";
	echo "			<div class='card' style='min-height: 120px;'>\n";
	echo "				<div class='card-body'>\n";
	echo "					<h5 class='card-title'>".$text['label-today_usage']."</h5>\n";
	echo "					<h2 class='card-text'>$".number_format($today_usage, 2)."</h2>\n";
	echo "					<small class='text-muted'>".$text['description-today_usage']."</small>\n";
	echo "				</div>\n";
	echo "			</div>\n";
	echo "		</div>\n";

	echo "	</div>\n"; //row
	echo "</div>\n"; //container

	//include recent activity section
	echo "<br>\n";
	echo "<div class='container-fluid'>\n";
	echo "	<div class='row'>\n";
	echo "		<div class='col-12'>\n";
	echo "			<h4>".$text['label-recent_usage']."</h4>\n";
	echo "			<table class='table table-striped'>\n";
	echo "				<thead>\n";
	echo "					<tr>\n";
	echo "						<th>".$text['label-call_date']."</th>\n";
	echo "						<th>".$text['label-extension']."</th>\n";
	echo "						<th>".$text['label-destination']."</th>\n";
	echo "						<th>".$text['label-duration']."</th>\n";
	echo "						<th class='text-right'>".$text['label-cost']."</th>\n";
	echo "					</tr>\n";
	echo "				</thead>\n";
	echo "				<tbody>\n";

	//get recent usage
	$sql = "select u.call_date, u.destination_number, u.duration, u.cost, u.currency, e.extension ";
	$sql .= "from v_billing_usage u ";
	$sql .= "left join v_extensions e on u.extension_uuid = e.extension_uuid ";
	$sql .= "where u.domain_uuid = :domain_uuid ";
	$sql .= "order by u.call_date desc ";
	$sql .= "limit 10 ";
	$parameters['domain_uuid'] = $domain_uuid;
	$database = new database;
	$recent_usage = $database->select($sql, $parameters, 'all');
	unset($sql, $parameters);

	if (is_array($recent_usage) && count($recent_usage) > 0) {
		foreach ($recent_usage as $row) {
			echo "				<tr>\n";
			echo "					<td>".escape($row['call_date'])."</td>\n";
			echo "					<td>".escape($row['extension'])."</td>\n";
			echo "					<td>".escape($row['destination_number'])."</td>\n";
			echo "					<td>".gmdate("H:i:s", $row['duration'])."</td>\n";
			echo "					<td class='text-right'>".number_format($row['cost'], 4)." ".escape($row['currency'])."</td>\n";
			echo "				</tr>\n";
		}
	}
	else {
		echo "				<tr>\n";
		echo "					<td colspan='5' class='text-center'>".$text['label-no_recent_usage']."</td>\n";
		echo "				</tr>\n";
	}

	echo "				</tbody>\n";
	echo "			</table>\n";
	echo "			<a href='billing_usage.php' class='btn btn-secondary'>".$text['button-view_all']."</a>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	echo "</div>\n";

//show the footer
	require_once "resources/footer.php";

?>
