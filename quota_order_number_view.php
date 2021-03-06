<?php
	$today = time();
	$today_string = date('Y-m-d', $today);
    $title = "View quota order number";
	require ("includes/db.php");
	$quota_order_number_id 	= get_querystring("quota_order_number_id");
	$quota_order_number_id 	= str_replace( ".", "", $quota_order_number_id);
	$quota_order_number 	= new quota_order_number;
	$quota_order_number->quota_order_number_id = $quota_order_number_id;
	$quota_order_number->get_origins();
	if (substr($quota_order_number->quota_order_number_id, 0, 3) == "094") {
		$quota_order_number->licensed = true;
		$type = "Licensed quota order number";
	} else {
		$quota_order_number->licensed = false;
		$type = "FCFS quota order number";
	}
	$type = "Quota order number";

	$quota_definition = new quota_definition;
	$quota_definition->clear_cookies();
	require ("includes/header.php");
?>

<div id="wrapper" class="direction-ltr">
	<div class="gem-c-breadcrumbs govuk-breadcrumbs " data-module="track-click">
		<ol class="govuk-breadcrumbs__list">
			<li class="govuk-breadcrumbs__list-item">
				<a class="govuk-breadcrumbs__link" href="/">Main menu</a>
			</li>
<?php
	if ($quota_order_number->licensed == true) {
?>
			<li class="govuk-breadcrumbs__list-item">
				<a class="govuk-breadcrumbs__link" href="/licensed_quota_order_numbers.html">Licensed quota order numbers</a>
			</li>
<?php
	} else {
?>
			<li class="govuk-breadcrumbs__list-item">
				<a class="govuk-breadcrumbs__link" href="/quota_order_numbers.html">Quota order numbers</a>
			</li>
<?php		
	}
?>		
			<li class="govuk-breadcrumbs__list-item">Quota <?=$quota_order_number_id?></li>
		</ol>
	</div>
	<div class="app-content__header">
    	<h1 class="govuk-heading-xl"><?=$type?> <?=$quota_order_number_id?></h1>
	</div>

<!-- Begin tabs //-->
<div class="govuk-tabs" data-module="govuk-tabs">
	<h2 class="govuk-tabs__title">Contents</h2>
	<ul class="govuk-tabs__list compact" style="max-width:100% !important;width:100%">
		<li class="govuk-tabs__list-item govuk-tabs__list-item--selected"><a class="govuk-tabs__tab" href="#details">Quota details</a></li>
		<li class="govuk-tabs__list-item"><a class="govuk-tabs__tab" href="#origins">Origins</a></li>
		<li class="govuk-tabs__list-item"><a class="govuk-tabs__tab" href="#definitions">Definition periods</a></li>
		<li class="govuk-tabs__list-item"><a class="govuk-tabs__tab" href="#associations">Associations</a></li>
		<li class="govuk-tabs__list-item"><a class="govuk-tabs__tab" href="#measures">Measures</a></li>
		<li class="govuk-tabs__list-item"><a class="govuk-tabs__tab" href="#commodities">Commodities</a></li>
	</ul>

	<!-- Begin details section //-->
	<section class="govuk-tabs__panel" id="details">
		<h2 style="margin-top:0px">Quota details</h2>
<?php
	$sql = "SELECT quota_order_number_sid, quota_order_number_id, validity_start_date, validity_end_date, description
	FROM quota_order_numbers WHERE quota_order_number_id = '" . $quota_order_number_id . "' ORDER BY validity_start_date DESC";
	$result = pg_query($conn, $sql);
	$quota_order_number_count = pg_num_rows($result);
	if (($result) && ($quota_order_number_count > 0)){
		if ($quota_order_number_count == 1) {
			echo ("<p>There is <strong>1 instance</strong> of quota order number <strong>" . $quota_order_number_id . "</strong>.</p>");
		} else {
			echo ("<p>There are <strong>" . $quota_order_number_count. "</strong> instances of quota order number <strong>" . $quota_order_number_id . "</strong>.</p>");
		}
?>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header nopad" style="width:8%">SID</th>
				<th class="govuk-table__header" style="width:18%">Quota order number ID</th>
				<th class="govuk-table__header" style="width:10%">Start date</th>
				<th class="govuk-table__header" style="width:10%">End date</th>
				<th class="govuk-table__header" style="width:44%">Description</th>
				<th class="govuk-table__header r" style="width:10%">Actions</th>
			</tr>
<?php            
		while ($row = pg_fetch_array($result)) {
			$quota_order_number_sid = $row["quota_order_number_sid"];
			$quota_order_number_id  = $row["quota_order_number_id"];
			$validity_start_date    = $row["validity_start_date"];
			$validity_end_date      = $row["validity_end_date"];
			$description			= $row["description"];
?>
				<tr class="govuk-table__row">
					<td class="govuk-table__cell nopad"><?=$quota_order_number_sid?></td>
					<td class="govuk-table__cell"><?=$quota_order_number_id?></td>
					<td class="govuk-table__cell"><?=short_date($validity_start_date)?></td>
					<td class="govuk-table__cell"><?=short_date($validity_end_date)?></td>
					<td class="govuk-table__cell"><?=$description?></td>
					<td class="govuk-table__cell r">
<?php
	if (($validity_end_date > $today_string) or (is_null($validity_end_date))) {
?>
						<form action="quota_order_number_create_edit.html" method="get">
							<input type="hidden" name="phase" value="edit" />
							<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
							<input type="hidden" name="quota_order_number_sid" value="<?=$quota_order_number_sid?>" />
							<button type="submit" class="govuk-button btn_nomargin">Edit</button>
						</form>
<?php
	}
?>						
					</td>
				</tr>
<?php
		}
?>
		</table>
<?php        
	}
?>

	</section>

	<section class="govuk-tabs__panel govuk-tabs__panel--hidden" id="origins">
		<h2 style="margin-top:0px">Quota origins</h2>
		<p>Please use this section to assign quota origins to this quota. An origin needs to
			be assigned to a quota to identify the origin country to which the quota applies.</p>
<?php
	$origin_count = count($quota_order_number->origins);
	if ($origin_count > 0) {
		if ($origin_count == 1) {
			echo ("<p>There is <strong>1 origin</strong> associated with this quota.</p>");
		} else {
			echo ("<p>There are <strong>" . $origin_count. "</strong> origins associated with quota order number <strong>" . $quota_order_number_id . "</strong>.</p>");
		}
?>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header nopad">Quota order number SID</th>
				<th class="govuk-table__header">Origin SID</th>
				<th class="govuk-table__header">Origin</th>
				<th class="govuk-table__header">Start date</th>
				<th class="govuk-table__header">End date</th>
				<th class="govuk-table__header">Exclusion(s)</th>
			</tr>
<?php
		for($i = 0; $i < $origin_count; $i++) {
			$origin					= $quota_order_number->origins[$i];
			$quota_order_number_sid	= $origin->quota_order_number_sid;
			$origin_sid				= $origin->quota_order_number_origin_sid;
			$geographical_area_id	= $origin->geographical_area_id;
			$description			= $origin->description;
			$origin->get_exclusion_text();
			$exclusion_text			= $origin->exclusion_text;
	?>
			<tr class="govuk-table__row">
				<td class="govuk-table__cell nopad"><?=$quota_order_number_sid?></td>
				<td class="govuk-table__cell"><?=$origin_sid?></td>
				<td class="govuk-table__cell"><a href="geographical_area_view.html?geographical_area_id=<?=$geographical_area_id?>"><?=$geographical_area_id?></a> (<?=$description?>)</td>
				<td class="govuk-table__cell"><?=short_date($origin->validity_start_date)?></td>
				<td class="govuk-table__cell"><?=short_date($origin->validity_end_date)?></td>
				<td class="govuk-table__cell"><?=$exclusion_text?></td>
			</tr>
<?php
		}
?>			
		</table>
<?php		
	} else {
		echo ("<p>There are currently no origins assigned to this quota.</a>");
	}
?>
		<form action="/quota_order_number_add_origin.html" method="get">
			<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
			<input type="hidden" name="quota_order_number_sid" value="<?=$quota_order_number_sid?>" />
			<input type="hidden" name="action" value="new" />
			<button type="submit" class="govuk-button small">Create new quota origin</button>
		</form>
	</section>

	<section class="govuk-tabs__panel govuk-tabs__panel--hidden" id="definitions">
		<h2 style="margin-top:0px">Quota definitions</h2>
		<p>Use this section to manage quota definition periods for this quota.</p>

<?php
	$sql = "SELECT quota_definition_sid, quota_order_number_id, validity_start_date, validity_end_date,
	quota_order_number_sid, initial_volume, qd.measurement_unit_code, maximum_precision,
	critical_state, critical_threshold, monetary_unit_code, measurement_unit_qualifier_code,
	qd.description as quota_definition_description, mud.description as measurement_unit_description
	FROM quota_definitions qd, measurement_unit_descriptions mud
	WHERE quota_order_number_id = '" . $quota_order_number_id . "'
	and qd.measurement_unit_code = mud.measurement_unit_code
	ORDER BY validity_start_date DESC";

	$result = pg_query($conn, $sql);
	if  (($result) && (pg_num_rows($result) > 0)){
?>
		<p>There are <strong><?=pg_num_rows($result)?></strong> definition periods associated with quota order number <strong><?=$quota_order_number_id?></strong>.</p>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header tiny nopad">Definition SID</th>
				<th class="govuk-table__header tiny c">Order number SID</th>
				<th class="govuk-table__header tiny">Start date</th>
				<th class="govuk-table__header tiny">End date</th>
				<th class="govuk-table__header tiny">Vol</th>
				<th class="govuk-table__header c tiny">Unit</th>
				<th class="govuk-table__header c tiny">Precision</th>
				<th class="govuk-table__header c tiny">Critical state</th>
				<th class="govuk-table__header c tiny">Critical threshold</th>
				<th class="govuk-table__header c tiny">Monetary unit</th>
				<th class="govuk-table__header tiny" style="width:20%">Description</th>
				<th class="govuk-table__header tiny c" style="width:7%">Actions</th>
			</tr>
<?php            
		while ($row = pg_fetch_array($result)) {
			$quota_definition_sid 				= $row["quota_definition_sid"];
			$quota_order_number_id  			= $row["quota_order_number_id"];
			$quota_order_number_sid  			= $row["quota_order_number_sid"];
			$validity_start_date                = $row["validity_start_date"];
			$validity_end_date                  = $row["validity_end_date"];
			$initial_volume                     = number_format($row["initial_volume"], 2);
			$measurement_unit_code              = $row["measurement_unit_code"];
			$maximum_precision                  = $row["maximum_precision"];
			$critical_state                     = $row["critical_state"];
			$critical_threshold                 = $row["critical_threshold"];
			$monetary_unit_code                 = $row["monetary_unit_code"];
			$measurement_unit_qualifier_code    = $row["measurement_unit_qualifier_code"];
			$description                        = $row["quota_definition_description"];
			$measurement_unit_description		= $row["measurement_unit_description"];
?>
			<tr class="govuk-table__row" id="def<?=$quota_definition_sid?>">
				<td class="govuk-table__cell tiny nopad"><?=$quota_definition_sid?></td>
				<td class="govuk-table__cell tiny c"><?=$quota_order_number_sid?></td>
				<td class="govuk-table__cell tiny" nowrap><?=short_date($validity_start_date)?></td>
				<td class="govuk-table__cell tiny" nowrap><?=short_date($validity_end_date)?></td>
				<td class="govuk-table__cell tiny"><?=$initial_volume?></td>
				<td class="govuk-table__cell c tiny"><abbr title="<?=$measurement_unit_description?>"><?=$measurement_unit_code?></abbr>&nbsp;<?=$measurement_unit_qualifier_code?></td>
				<td class="govuk-table__cell c tiny"><?=$maximum_precision?></td>
				<td class="govuk-table__cell c tiny"><?=$critical_state?></td>
				<td class="govuk-table__cell c tiny"><?=$critical_threshold?></td>
				<td class="govuk-table__cell c tiny"><?=$monetary_unit_code?></td>
				<td class="govuk-table__cell tiny"><?=$description?></td>
				<td class="govuk-table__cell c nopad">
<?php
	if (($validity_end_date > $today_string) or (is_null($validity_end_date))) {
?>
					<form action="quota_definition_create_edit.html" method="get">
						<input type="hidden" name="action" value="edit" />
						<input type="hidden" name="quota_definition_sid" value="<?=$quota_definition_sid?>" />
						<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
						<button type="submit" class="govuk-button btn_nomargin">Edit</button>
					</form>
<?php
	}
	if (($validity_end_date > $today_string) or (is_null($validity_start_date))) {
?>
					<form action="actions/quota_definition_actions.html" method="get">
						<input type="hidden" name="action" value="delete" />
						<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
						<input type="hidden" name="quota_definition_sid" value="<?=$quota_definition_sid?>" />
						<button type="submit" class="govuk-button btn_nomargin">Delete</button>
					</form>
					<?php		
	}					
?>
				</td>
			</tr>
<?php
		}
?>
		</table>
<?php        
	}
?>
		<form action="/quota_definition_create_edit.html" method="get" xclass="inline_form">
			<input type="hidden" name="quota_order_number_id" value="<?=$quota_order_number_id?>" />
			<input type="hidden" name="action" value="new" />
			<button type="submit" class="small govuk-button">Create new quota definition</button>
		</form>


	</section>
	<!-- End quota definitions //-->

	<!-- Start quota associations //-->
	<section class="govuk-tabs__panel govuk-tabs__panel--hidden" id="associations">
		<h2 style="margin-top:0px">Quota associations</h2>
<?php
	$sql = "select qdm.quota_order_number_id as main_quota_order_number_id, qdm.quota_definition_sid as main_quota_definition_sid,
	qds.quota_order_number_id as sub_quota_order_number_id, qds.quota_definition_sid as sub_quota_definition_sid, 
	qdm.validity_start_date as main_start_date, qds.validity_start_date as sub_start_date, 
	qdm.validity_end_date as main_end_date, qds.validity_end_date as sub_end_date, qa.relation_type, qa.coefficient,
	qdm.initial_volume as main_volume, qdm.measurement_unit_code as main_unit, qdm.measurement_unit_qualifier_code as main_qualifier, 
	qds.initial_volume as sub_volume, qds.measurement_unit_code as sub_unit, qdm.measurement_unit_qualifier_code as sub_qualifier
	from quota_associations qa, quota_definitions qdm, quota_definitions qds
	where qa.main_quota_definition_sid = qdm.quota_definition_sid
	and qa.sub_quota_definition_sid = qds.quota_definition_sid
	and (qdm.quota_order_number_id = '" . $quota_order_number_id . "' or qds.quota_order_number_id = '" . $quota_order_number_id . "')
	order by main_quota_order_number_id, sub_quota_order_number_id, main_start_date desc, sub_start_date desc";
	$result = pg_query($conn, $sql);
	if  (($result) && (pg_num_rows($result) > 0)){
?>
		<p>There are <strong><?=pg_num_rows($result)?></strong> associations on this quota.</p>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header cell_grey tiny">main quota order number id</th>
				<th class="govuk-table__header cell_grey tiny">main quota definition sid</th>
				<th class="govuk-table__header cell_grey tiny">main dates</th>
				<th class="govuk-table__header cell_grey tiny">main balance</th>
				<th class="govuk-table__header tiny">sub quota order number id</th>
				<th class="govuk-table__header tiny">sub quota definition sid</th>
				<th class="govuk-table__header tiny">sub dates</th>
				<th class="govuk-table__header tiny">sub balance</th>
				<th class="govuk-table__header c cell_grey tiny">relation type</th>
				<th class="govuk-table__header r cell_grey tiny">coefficient</th>
			</tr>
<?php            
		while ($row = pg_fetch_array($result)) {
			$main_quota_order_number_id	= $row["main_quota_order_number_id"];
			$main_quota_definition_sid  = $row["main_quota_definition_sid"];
			$sub_quota_order_number_id  = $row["sub_quota_order_number_id"];
			$sub_quota_definition_sid  	= $row["sub_quota_definition_sid"];
			$main_start_date            = short_date($row["main_start_date"]);
			$sub_start_date             = short_date($row["sub_start_date"]);
			$main_end_date            	= short_date($row["main_end_date"]);
			$sub_end_date             	= short_date($row["sub_end_date"]);
			$relation_type              = $row["relation_type"];
			$coefficient                = number_format($row["coefficient"], 5);

			$main_volume				= $row["main_volume"];
			$main_unit					= $row["main_unit"];
			$main_qualifier				= $row["main_qualifier"];
			$sub_volume					= $row["sub_volume"];
			$sub_unit					= $row["sub_unit"];
			$sub_qualifier				= $row["sub_qualifier"];
		?>
			<tr class="govuk-table__row">
<?php
	if ($main_quota_order_number_id == $quota_order_number_id) {
?>
				<td class="govuk-table__cell cell_grey tiny"><?=$main_quota_order_number_id?></td>
<?php
	} else {
?>
				<td class="govuk-table__cell cell_grey tiny"><a href="/quota_order_number_view.html?quota_order_number_id=<?=$main_quota_order_number_id?>#associations"><?=$main_quota_order_number_id?></a></td>
<?php		
	}
?>
				<td class="govuk-table__cell cell_grey tiny"><?=$main_quota_definition_sid?></td>
				<td class="govuk-table__cell cell_grey tiny" nowrap><?=$main_start_date?> to <?=$main_end_date?></td>
				<td class="govuk-table__cell cell_grey tiny"><?=$main_volume?> <?=$main_unit?> <?=$main_qualifier?></td>
				<?php
	if ($sub_quota_order_number_id == $quota_order_number_id) {
?>
				<td class="govuk-table__cell tiny"><?=$sub_quota_order_number_id?></td>
<?php
	} else {
?>
				<td class="govuk-table__cell tiny"><a href="/quota_order_number_view.html?quota_order_number_id=<?=$sub_quota_order_number_id?>#associations"><?=$sub_quota_order_number_id?></a></td>
<?php		
	}
?>

				<td class="govuk-table__cell tiny"><?=$sub_quota_definition_sid?></td>
				<td class="govuk-table__cell tiny" nowrap><?=$sub_start_date?> to <?=$sub_end_date?></td>
				<td class="govuk-table__cell tiny"><?=$sub_volume?> <?=$sub_unit?> <?=$sub_qualifier?></td>
				<td class="govuk-table__cell c cell_grey tiny"><?=$relation_type?></td>
				<td class="govuk-table__cell r cell_grey tiny"><?=$coefficient?></td>
			</tr>
<?php
		}
?>
		</table>
<?php        
	} else {
?>
		<P>There are no quota associations on this quota.</p>
<?php		
	}
?>

	</section>
	<!-- End quota associations //-->


	<!-- Start measures //-->
	<section class="govuk-tabs__panel govuk-tabs__panel--hidden" id="measures">
		<h2 style="margin-top:0px">Measures</h2>
<?php
	$sql = "SELECT measure_sid, m.measure_type_id, m.geographical_area_id, goods_nomenclature_item_id, m.validity_start_date,
	m.validity_end_date, measure_generating_regulation_id, mtd.description as measure_type_description,
	m.goods_nomenclature_sid, ga.description as geographical_area_description
	FROM ml.measures_real_end_dates m, measure_type_descriptions mtd, ml.ml_geographical_areas ga
	WHERE ordernumber = '" . $quota_order_number_id . "'
	and m.measure_type_id = mtd.measure_type_id
	and m.geographical_area_id = ga.geographical_area_id
	ORDER BY validity_start_date DESC, goods_nomenclature_item_id, m.geographical_area_id";
	$result = pg_query($conn, $sql);
	if  (($result) && (pg_num_rows($result) > 0)){
?>
		<p>There are <strong><?=pg_num_rows($result)?> measures</strong> associated with quota order number <strong><?=$quota_order_number_id?></strong>.</p>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header small nopad" style="width:8%">SID</th>
				<th class="govuk-table__header small" style="width:24%">Measure type</th>
				<th class="govuk-table__header small" style="width:22%">Geographical area</th>
				<th class="govuk-table__header small" style="width:20%">Commodity</th>
				<th class="govuk-table__header small" style="width:8%">Start date</th>
				<th class="govuk-table__header small" style="width:8%">End date</th>
				<th class="govuk-table__header small r" style="width:10%">Regulation</th>
			</tr>
<?php
		while ($row = pg_fetch_array($result)) {
			$measure_sid                	= $row['measure_sid'];
			$measure_type_id            	= $row['measure_type_id'];
			$measure_type_description		= $row['measure_type_description'];
			$geographical_area_id       	= $row['geographical_area_id'];
			$geographical_area_description	= $row['geographical_area_description'];
			$goods_nomenclature_item_id 	= $row['goods_nomenclature_item_id'];
			$goods_nomenclature_sid			= $row['goods_nomenclature_sid'];
			$regulation_id_full         	= $row['measure_generating_regulation_id'];
			$validity_start_date        	= short_date($row['validity_start_date']);
			$validity_end_date          	= short_date($row['validity_end_date']);
			
			$commodity_url                  = "/goods_nomenclature_item_view.html?goods_nomenclature_item_id=" . $goods_nomenclature_item_id
?>
			<tr class="govuk-table__row">
				<td class="govuk-table__cell small nopad"><a href="measure_view.html?measure_sid=<?=$measure_sid?>"><?=$measure_sid?></a></td>
				<td class="govuk-table__cell small"><a href="measure_type_view.html?measure_type_id=<?=$measure_type_id?>"><?=$measure_type_id?>&nbsp;<?=$measure_type_description?></a></td>
				<td class="govuk-table__cell small"><a href="geographical_area_view.html?geographical_area_id=<?=$geographical_area_id?>"><?=$geographical_area_id?>&nbsp;<?=$geographical_area_description?></a></td>
				<td class="govuk-table__cell small"><a class="nodecorate" href="<?=$commodity_url?>" data-lity data-lity-target="<?=$commodity_url?>?>"><?=format_goods_nomenclature_item_id($goods_nomenclature_item_id)?></a>&nbsp;[<?=$goods_nomenclature_sid?>]</td>
				<td class="govuk-table__cell small" nowrap><?=$validity_start_date?></td>
				<td class="govuk-table__cell small" nowrap><?=$validity_end_date?></td>
				<td class="govuk-table__cell small r"><a href="regulation_view.html?base_regulation_id=<?=$regulation_id_full?>"><?=$regulation_id_full?></a></td>
			</tr>
<?php
		}
?>
		</table>
<?php
	} else {
		echo ("<p>There are no measures featuring this quota.");
	}
?>
	</section>
	<!-- End quota measures //-->

	<!-- End quota commodities //-->
	<section class="govuk-tabs__panel govuk-tabs__panel--hidden" id="commodities">
		<h2 style="margin-top:0px">Commodities</h2>
		<p>Any commodity highlighted in pink has an end-date associated with it, and therefore will
			not necessarily feature in any future quotas.</p>
<?php
	$sql = "SELECT distinct on (m.goods_nomenclature_item_id) m.goods_nomenclature_item_id, gd.description,
	fn.description as friendly, g.validity_end_date
	FROM goods_nomenclatures g, measures m, goods_nomenclature_descriptions gd
	left outer join ml.commodity_friendly_names fn on left(gd.goods_nomenclature_item_id, 8) = fn.goods_nomenclature_item_id
	WHERE m.goods_nomenclature_item_id = gd.goods_nomenclature_item_id and gd.productline_suffix = '80'
	and g.goods_nomenclature_item_id = gd.goods_nomenclature_item_id
	and g.producline_suffix = gd.productline_suffix
	and m.ordernumber = '" . $quota_order_number_id . "'
	ORDER BY 1, gd.goods_nomenclature_description_period_sid desc";

	$result = pg_query($conn, $sql);
	if  (($result) && (pg_num_rows($result) > 0)){
?>
		<p>
			There are <strong><?=pg_num_rows($result)?> commodities</strong> associated with quota order number <?=$quota_order_number_id?>.
			&nbsp;&nbsp;<a target="_blank" href="https://ec.europa.eu/taxation_customs/dds2/taric/quota_tariff_details.jsp?Lang=en&StartDate=2019-01-01&Code=<?=$quota_order_number_id?>">Check on EU Taric quota consultation site</a>
		</p>
		<table class="govuk-table" cellspacing="0">
			<tr class="govuk-table__row">
				<th class="govuk-table__header small" style="width:10%">Commodity</th>
				<th class="govuk-table__header small" style="width:45%">Description</th>
				<th class="govuk-table__header small" style="width:45%">Friendly description *</th>
			</tr>
<?php
		while ($row = pg_fetch_array($result)) {
			$goods_nomenclature_item_id 	= $row['goods_nomenclature_item_id'];
			$goods_nomenclature_item_id2	= reduce($goods_nomenclature_item_id);
			$description					= $row['description'];
			$friendly						= $row['friendly'];
			$validity_end_date				= $row['validity_end_date'] . "";

			$cellclass = "";
			if ($validity_end_date != "") {
				$cellclass = "warning";
				$description .= " (<strong>ends " . short_date($validity_end_date) . "</strong>)";
			}
			
			$commodity_url = "/goods_nomenclature_item_view.html?goods_nomenclature_item_id=" . $goods_nomenclature_item_id
?>
			<tr class="govuk-table__row">
				<td class="govuk-table__cell small <?=$cellclass?>"><a class="nodecorate" href="<?=$commodity_url?>" data-lity data-lity-target="<?=$commodity_url?>?>"><?=format_goods_nomenclature_item_id($goods_nomenclature_item_id)?></a></td>
				<td class="govuk-table__cell small <?=$cellclass?>"><?=$description?></td>
				<td class="govuk-table__cell small <?=$cellclass?>"><span style="padding-right:1em"><?=$friendly?></span><a target="_blank" href="https://www.tariffnumber.com/2019/<?=$goods_nomenclature_item_id2?>">View</a></td>
			</tr>
<?php
		}
?>
		</table>
		<p>* Please note: the 'friendly descriptions' are derived from the website '<a target="_blank" href="https://www.tariffnumber.com/2019/611530">tariffnumber.com</a>'.</p>
<?php
	} else {
		echo ("<p>There are no commodities associated with quota order number " . $quota_order_number_id . ".");
	}
?>
	</section>
</div>
</div>
<?php
	require ("includes/footer.php");
?>