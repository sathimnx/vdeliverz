@extends('demand.layouts.main')

@section('content')

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
@endpush

    <!-- Scroll - horizontal and vertical table -->
{{--    <h5><b>Dashboard</b></h5> <br />--}}
    <section id="horizontal-vertical">
        <div class="content-body">
            <div class="row">
                <div class="col-md-6">
                    @role('admin')
                    <label for="">Select Shop</label>
                    <select name="" id="filterByType" onchange="filterOrder('{{env('APP_URL')}}')" class="form-control select2">
                            <option value="all" {{request()->type == null  ? 'selected' : ''}}>All</option>
                        @forelse($shops as $shop)
                            <option value="{{$shop->id}}" {{request()->shop == $shop->id  ? 'selected' : ''}}>{{$shop->name}}</option>
                        @empty
                        @endforelse
                    </select>

                    @endrole
                </div>
                @push('scripts')
                    <script>
                        function filterOrder(url){
                            var month = $("#selectMonth").val();
                            var shop = $("#filterByType").val();
                            var year = $("#selectYear").val();
                            window.location.href = url+'demand/dashboard/'+shop+'/'+year+'/'+month;
                        }
                    </script>
                @endpush
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card border text-center my-1">
                        <div class="card-body p-1">
                        <p class="text-muted">Total Bookings Completed <br> <b>{{ $completed }}</b></p>
                        </div>
                      </div>
                </div>
                <div class="col-md-3">
                    <div class="card border text-center my-1">
                        <div class="card-body p-1">
                        <p class="text-muted">Total Earnings <br> <b>{{ $tot_earnings }} ₹</b></p>
                        </div>
                      </div>
                </div>
                <div class="col-md-3">
                    <div class="card border text-center my-1">
                        <div class="card-body p-1">
                        <p class="text-muted">Total Bookings Pending <br> <b>{{ $pending }}</b></p>
                        </div>
                      </div>
                </div>
                <div class="col-md-3">
                    <div class="card border text-center my-1">
                        <div class="card-body p-1">
                        <p class="text-muted"> {{ config('app.name') }} Customers <br> <b>{{ $users }}</b></p>
                        </div>
                      </div>
                </div>
                <!-- Column Chart -->
                <div class="col-lg-12 col-md-12 pt-5" id="monthlyChart">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between">
                                <h4 class="card-title">Monthly Chart</h4>
                                <div class="form-group w-25">
                                    <label for="exampleFormControlSelect1">Select Year:</label>
                                    <?php  $current_year = date("Y"); $sel = request()->year;?>
                                    <select class="form-control" onchange="change_year('{{env('APP_URL')}}', this.value)" id="exampleFormControlSelect1">
                                        @for ($i = $current_year; $i >= 2021; $i--)
                                            <option value="{{$i}}" {{$sel == $i ? 'selected' : null}}>{{$i}}</option>
                                        @endfor

                                    </select>
                                </div>
                            </div>
                        </div>

                    <div class="card-content">
                        <div class="card-body">
                        <div id="yearly-chart"></div>
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Column Chart -->

                <!-- Column Chart -->
                <div class="col-lg-12 col-md-12 pt-5" id="weeklyChart">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between">
                                <h4 class="card-title">Weekly Chart</h4>
                                <div class="form-group w-25">
                                    <label for="selectYear">Select Year:</label>
                                    <?php  $current_year = date("Y"); $sel = request()->year ?? $current_year;?>
                                    <select class="form-control" onchange="changing_both('{{env('APP_URL')}}')" id="selectYear">
                                        @for ($i = $current_year; $i >= 2021; $i--)
                                            <option value="{{$i}}" {{$sel == $i ? 'selected' : null}}>{{$i}}</option>
                                        @endfor

                                    </select>
                                </div>
                                <div class="form-group w-25">
                                    <label for="selectMonth">Select month:</label>
                                    <?php  $current_month = date("m"); $selec = request()->month ?? $current_month;?>
                                    <select class="form-control" onchange="changing_both('{{env('APP_URL')}}')" id="selectMonth">
                                        @for ($j = 1; $j <= 12; $j++)
                                            <option value="{{$j}}" {{$selec == $j ? 'selected' : null}}>{{date("F", mktime(0, 0, 0, $j, 10))}}</option>
                                        @endfor

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div id="weekly-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--/ Scroll - horizontal and vertical table -->

@push('scripts')

<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/charts/chart-apex.min.js') }}"></script>
<script>
    function change_year(url, year){
        var shop = $("#filterByType").val();
        window.location.href = 'demand/dashboard/'+shop+'/'+year+'/1#monthlyChart'
    }
    function changing_both(url){
        var month = $("#selectMonth").val();
        var shop = $("#filterByType").val();
        var year = $("#selectYear").val();
        window.location.href = url+'demand/dashboard/'+shop+'/'+year+'/'+month+'#weeklyChart'
    }
</script>
<script>

    var e = "#5A8DEE",
    t = [e, "#FDAC41", "#FF5B5C", "#39DA8A", "#00CFDD"];
    var r = {
    chart: { height: 350, type: "bar" },
    colors: t,
    plotOptions: {
        bar: { horizontal: !1, endingShape: "rounded", columnWidth: "55%" }
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ["transparent"] },
    series: [
        // { name: "Net Profit", data: [44, 55, 57, 56, 61, 58, 63, 60, 66] },
        // { name: "Revenue", data: [76, 85, 101, 98, 87, 105, 91, 114, 94] },
        {
            name: "Total Sales Amount",
            data: {{json_encode($final)}}
        }
    ],
    legend: { offsetY: -10 },
    xaxis: {
        categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec"
        ]
    },
    yaxis: { title: { text: " ₹" } },
    fill: { opacity: 1 },
    tooltip: {
        y: {
            formatter: function(e) {
                return e + " ₹";
            }
        }
    }
};
new ApexCharts(document.querySelector("#yearly-chart"), r).render();

</script>

<script>

    var e = "#5A8DEE",
    t = [e, "#FDAC41", "#FF5B5C", "#39DA8A", "#00CFDD"];
    var w = {
    chart: { height: 350, type: "bar" },
    colors: t,
    plotOptions: {
        bar: { horizontal: !1, endingShape: "rounded", columnWidth: "55%" }
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ["transparent"] },
    series: [
        // { name: "Net Profit", data: [44, 55, 57, 56, 61, 58, 63, 60, 66] },
        // { name: "Revenue", data: [76, 85, 101, 98, 87, 105, 91, 114, 94] },
        {
            name: "Total Sales Amount",
            data: {{json_encode($daily)}}
        }
    ],
    legend: { offsetY: -10 },
    xaxis: {
        categories: {{json_encode($dates)}}
    },
    yaxis: { title: { text: " ₹" } },
    fill: { opacity: 1 },
    tooltip: {
        y: {
            formatter: function(e) {
                return e + " ₹" ;
            }
        }
    }
};
new ApexCharts(document.querySelector("#weekly-chart"), w).render();

</script>
@endpush


@endsection
