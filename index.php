<?php
    $title = "Main menu";
	require ("includes/db.php");
	require ("includes/header.php");
	?>
			<div class="app-content__header">
				<h1 class="govuk-heading-xl">Main menu</h1>
			</div>
			<div class="app-prose-scope">
				<div class="column-one-third" style="width:23%;padding-right:1%">
					<h2 class="small">Measures</h2>
					<ul class="main-menu">
						<li><a href="/measures.html">Find and edit measures</a></li>
						<li><a href="/measure_create_edit.html">Create measures</a></li>
					</ul>

					<h2 class="small">Regulations</h2>
					<ul class="main-menu">
						<li><a href="/regulations.html">Find and edit regulations</a></li>
						<li><a href="/regulation_create_edit.html?action=new&phase=regulation_create">Create a new regulation</a></li>
					</ul>

					<h2 class="small">Quotas</h2>
					<ul class="main-menu">
						<li><a href="/quota_order_numbers.html">FCFS quota order numbers</a></li>
						<li><a href="/licensed_quota_order_numbers.html">Licensed quota order numbers</a></li>
						<li><a href="/quota_order_number_create_edit.html">Create a quota</a></li>
						<li><a href="/quota_associations.html">View quota associations</a></li>
						<!--<li><a href="/quota_association_create.html">Create quota association</a></li>//-->
						<li><a href="/quota_blocking_periods.html">View quota blocking periods</a></li>
						<!--<li><a href="/quota_blocking_period_create.html">Create quota blocking period</a></li>//-->
						<li><a href="/quota_suspension_periods.html">View quota suspension periods</a></li>
						<!--<li><a href="/quota_suspension_period_create.html">Create quota suspension period</a></li>//-->
					</ul>

				</div>


				<div class="column-one-third" style="width:23%;padding-right:1%">
					<h2 class="small">Geographical areas</h2>
					<ul class="main-menu">
						<li><a href="/geographical_areas.html">Geographical areas</a></li>
					</ul>

					<h2 class="small">Measure types</h2>
					<ul class="main-menu">
						<li><a href="/measure_types.html">Measure types</a></li>
						<li><a href="/measure_type_create_edit.html?action=new&phase=create">Create measure type</a></li>
					</ul>

					<h2 class="small">Certificates</h2>
					<ul class="main-menu">
						<li><a href="/certificates.html">Certificates</a></li>
						<li><a href="/certificate_create_edit.html">Create certificate</a></li>
					</ul>

					<h2 class="small">Certificate types</h2>
					<ul class="main-menu">
						<li><a href="/certificate_types.html">Certificate types</a></li>
						<li><a href="/certificate_types.html">Create certificate type</a></li>
					</ul>



				</div>


				<div class="column-one-third" style="width:23%;padding-right:1%">
					<h2 class="small">Goods classification</h2>
					<ul class="main-menu">
						<li><a href="/sections.html">Search or browse goods classification</a></li>
					</ul>

					<h2 class="small">Additional codes</h2>
					<ul class="main-menu">
						<li><a href="/additional_codes.html">Additional codes</a></li>
						<li><a href="/additional_code_types.html">Additional code types</a></li>
					</ul>

					<h2 class="small">Footnotes</h2>
					<ul class="main-menu">
						<li><a href="/footnotes.html">Footnotes</a></li>
						<li><a href="/footnote_create_edit.html">Create footnote</a></li>
					</ul>

					<h2 class="small">Footnote types</h2>
					<ul class="main-menu">
						<li><a href="/footnote_types.html">Footnote types</a></li>
						<li><a href="/footnote_type_create_edit.html">Create footnote type</a></li>
					</ul>

					<h2 class="small">Audit</h2>
					<ul class="main-menu">
						<li><a href="/audit_form.html">View audit trail</a></li>
					</ul>

				</div>


				<div class="column-one-third" style="width:23%;padding-right:1%">
				<h2 class="small">Load history</h2>
					<ul class="main-menu">
						<li><a href="/load_history.html">Load history</a></li>
						<li><a href="/transition_progress.html">Transition progress</a></li>
						<li><a href="/coverage.html">Coverage</a></li>
					</ul>

					
<?php
	if (strpos($http_host, "dev") > -1) {
?>
					<h2 class="small">Data extract</h2>
					<ul class="main-menu">
						<li><a href="/extract_date_set.html">Set extract date time</a></li>
						<li><a href="/data_extract.html">Extract latest data set</a></li>
					</ul>

<?php        
	}
?>
<h2 class="small">Reference data</h2>
					<ul class="main-menu">
						<li><a href="/measurement_units.html">Measurement units</a></li>
						<li><a href="/measurement_unit_qualifiers.html">Measurement unit qualifiers</a></li>
						<li><a href="/regulation_groups.html">Regulation groups</a></li>
						<li><a href="/measure_type_series.html">Measure type series</a></li>
					</ul>

					<h2 class="small">Workbaskets</h2>
					<ul class="main-menu">
						<li><a href="/reassign_workbasket.html">Reassign workbasket</a></li>
					</ul>



				</div>
			<div class="clearer"><!--&nbsp;//--></div>
		</div>
</div>

<?php
	require ("includes/footer.php")
?>