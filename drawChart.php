<!DOCTYPE html>
<html lang="en">
<head>
    <title>CSV Uploading</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
        integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
        crossorigin="anonymous">
</head>
<body>
    <div style="margin-top: 40px;">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h2>Please Select CSV File</h2>
            </div>
        </div>

        <div class="row">
            <form method="post" id="frmMain" enctype="multipart/form-data" action="async-uploadCsv.php">
                <input type="hidden" name="upload" value="1">
                <div class="col-sm-6 col-sm-offset-3">
                    <input type="file" name="file" class="form-control" id="file"/>
                </div>
            </form>
        </div>

        <div id="js-div-draw" class="hide">
            <div class="row">
                <div class="col-sm-4 col-sm-offset-2" style="margin-top: 10px;">
                    <select class="form-control" id="js-select-column">
                    </select>
                </div>
                <div class="col-sm-4" style="margin-top: 10px;">
                    <select class="form-control" id="js-select-column2">
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-sm-offset-3" style="margin-top: 10px;">
                    <button class="btn btn-primary btn-lg btn-block" id="js-btn-draw">
                        Draw Chart
                    </button>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div style="width: 100%; height: 400px;" id="js-div-chart1">
                </div>
            </div>
            <div class="col-sm-12">
                <div style="width: 100%; height: 400px;" id="js-div-chart2">
                </div>
            </div>
            <div class="col-sm-12">
                <div style="width: 100%; height: 400px;" id="js-div-chart3">
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript"
            src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <script>
        var filename = '';
        var dataRaw;
        var columnName = [];
        var dataMain;
        var dataMain2;
        var is_found = false;
        $(document).ready(function() {
            $("input#file").change( function(){
                $(this).parents("form").ajaxForm({
                    success : fnUploaded
                }).submit();
            });

            $("button#js-btn-draw").click(function() {
                var graphData = [];
                var graphData2 = [];
                var graphData3 = [];
                var tempGraphData3 = [];
                var columnName = $("select#js-select-column").val();
                var columnName2 = $("select#js-select-column2").val();
                is_found = false;
                fnExtractData(dataRaw, columnName2);
                dataMain2 = dataMain;
				console.log(dataMain2);
                is_found = false;
                fnExtractData(dataRaw, columnName);
                var index = 0;
                graphData[index] = ['Time', columnName];
                graphData2[index] = ['Time', columnName, columnName2];
                index++;

                graphData3[0] = ['Monthly', columnName];

                $.each(dataMain , function (key, value) {
                    if (!(value instanceof Object) && /\d/.test(key)) {
                        graphData[index] = [key, (value + "").replace(',', '') * 1];
                        graphData2[index] = [key, (value + "").replace(',', '') * 1, (dataMain2[key] + "").replace(',', '') * 1];
                        index++;
                    }
                });

                for (var i = 1; i <= 12; i++) {
                    if (i < 10) {
                        tempGraphData3['0' + i] = 0;
                    } else {
                        tempGraphData3[i + ''] = 0;
                    }
                }

                $.each(dataMain , function (key, value) {
                    if (!(value instanceof Object) && /\d/.test(key)) {
                        var month = key.split('-')[0];
                        tempGraphData3[month] = tempGraphData3[month] + (value + "").replace(',', '') * 1;
                    }
                });

                for (var i = 1; i <= 12; i++) {
                    if (i < 10) {
                        graphData3[i] = ["0" + i, tempGraphData3["0" + i]];
                    } else {
                        graphData3[i] = [i + "", tempGraphData3[i + ""]];
                    }
                }

                var data = google.visualization.arrayToDataTable(graphData);
                var data2 = google.visualization.arrayToDataTable(graphData2);
                var data3 = google.visualization.arrayToDataTable(graphData3);
                var options1 = {
                    title: columnName + "(Bar Chart All)",
                    bars: 'vertical',
                    series: {
                        0: { axis: 'distance' } // Bind series 0 to an axis named 'distance'.
                    },
                    axes: {
                        x: {
                            distance: {label: 'parsecs'} // Bottom x-axis.
                        }
                    }
                };

                var options2 = {
                    title: columnName + "(Line Chart All)",
                    curveType: 'function',
                    pointSize: 5,
                    legend: { position: 'bottom' }
                };

                var options3 = {
                    title: columnName + "(Line Chart Monthly)",
                    curveType: 'function',
                    pointSize: 5,
                    legend: { position: 'bottom' }
                };


                var chart1 = new google.visualization.BarChart(document.getElementById('js-div-chart1'));
                chart1.draw(data, options1);

                var chart2 = new google.visualization.LineChart(document.getElementById('js-div-chart2'));
                chart2.draw(data2, options2);

                var chart3 = new google.visualization.LineChart(document.getElementById('js-div-chart3'));
                chart3.draw(data3, options3);
            });
        });

        function fnExtractData(data, columnName) {
            if (is_found == false) {
                $.each(data , function (key, value) {
                    if (is_found == false) {
                        if (key == columnName) {
                            is_found = true;
                            dataMain = value;
                            return;
                        } else if (value instanceof Object) {
                            fnExtractData(value, columnName);
                        }
                    }
                });
            }
        }

        function fnUploaded(data) {
            filename = data;
            $("div#js-div-draw").removeClass('hide');

            $.ajax({
                url: "async-getData.php",
                dataType : "json",
                type : "POST",
                data : {filename : filename},
                success : function(result) {
                    dataRaw = result.data;
                    columnName = result.column;
                    var strHTML = "";
                    for (var i = 0; i < columnName.length; i++) {
                        strHTML += "<option value='" + columnName[i] +"'>" + columnName[i] +"</option>";
                        $("select#js-select-column").html(strHTML);
                        $("select#js-select-column2").html(strHTML);
                    }
                }
            });
        }

    </script>
</body>
</html>
