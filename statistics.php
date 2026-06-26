<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="index.php?dashboard"><em class="fa fa-home"></em></a></li>
            <li class="active"><?php _e('statistics_title'); ?></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-pie-chart"></i> <?php _e('statistics_panel'); ?></div>
                <div class="panel-body">

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load("current", {packages:["corechart"]});
google.charts.setOnLoadCallback(drawChart1);
function drawChart1() {
    var data = google.visualization.arrayToDataTable([
        ['<?php _e('statistics_employee_label') ?>', '<?php _e('statistics_quantity') ?>'],
        ['<?php _e('statistics_manager') ?>', 2],
        ['<?php _e('statistics_cleaning') ?>', 14],
        ['<?php _e('statistics_receptionist') ?>', 4],
        ['<?php _e('statistics_cook') ?>', 5],
    ]);
    var options = { title: '<?php echo __('statistics_employees'); ?>', is3D: true, };
    new google.visualization.PieChart(document.getElementById('piechart_3d')).draw(data, options);
}
</script>
<script type="text/javascript">
google.charts.load("current", {packages:["corechart"]});
google.charts.setOnLoadCallback(drawChart2);
function drawChart2() {
    var data = google.visualization.arrayToDataTable([
        ['<?php _e('statistics_type') ?>', '<?php _e('statistics_expense') ?>', { role: "style" } ],
        ['<?php _e('statistics_maintenance') ?>', 8.94, "#b87333"],
        ['<?php _e('statistics_salaries') ?>', 10.49, "silver"],
        ['<?php _e('statistics_electricity') ?>', 19.30, "gold"],
        ['<?php _e('statistics_external_services') ?>', 21.45, "color: #e5e4e2"]
    ]);
    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2]);
    var options = { title: "<?php echo __('statistics_expenses'); ?>", width: 410, height: 400, bar: {groupWidth: "95%"}, legend: { position: "none" } };
    new google.visualization.BarChart(document.getElementById("barchart_values")).draw(view, options);
}
</script>
<script type="text/javascript">
google.charts.load("current", {packages:["calendar"]});
google.charts.setOnLoadCallback(drawChart3);
function drawChart3() {
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn({ type: 'date', id: 'Date' });
    dataTable.addColumn({ type: 'number', id: '<?php _e('statistics_booked_rooms') ?>' });
    dataTable.addRows([
        [new Date(2020,3,13), 6], [new Date(2020,3,14), 7], [new Date(2020,3,15), 2], [new Date(2020,3,16), 3], [new Date(2020,3,17), 3],
        [new Date(2020,4,13), 5], [new Date(2020,4,14), 9], [new Date(2020,4,15), 5], [new Date(2020,4,16), 6], [new Date(2020,4,17), 2],
        [new Date(2020,9,4), 3], [new Date(2020,9,5), 5], [new Date(2020,9,12), 6], [new Date(2020,9,13), 7],
        [new Date(2020,9,19), 1], [new Date(2020,9,23), 3], [new Date(2020,9,24), 5], [new Date(2020,9,30), 2]
    ]);
    var options = { title: "<?php echo __('statistics_reservations'); ?>", height: 350, };
    new google.visualization.Calendar(document.getElementById('calendar_basic')).draw(dataTable, options);
}
</script>

<div class="row">
    <div class="col-md-6">
        <div id="piechart_3d" style="height:400px;"></div>
    </div>
    <div class="col-md-6">
        <div id="barchart_values" style="height:400px;"></div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div id="calendar_basic" style="height:350px;"></div>
    </div>
</div>

                </div>
            </div>
        </div>
    </div>
</div>
