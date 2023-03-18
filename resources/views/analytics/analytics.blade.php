<!DOCTYPE html>

<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="{{ config('app.name') }} Adminpanel">
    <meta name="keywords" content="Developed using Laravel">
    <meta name="author" content="MINDNOTIX">
    <title>{{ config('app.name') }} Analytics</title>
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/fav.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/fav.ico') }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <!-- END: Vendor CSS-->

    <!--Icons -->
    <link href='https://unpkg.com/boxicons@2.0.5/css/boxicons.min.css' rel='stylesheet'>

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/dragula.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-ecommerce.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-analytics.min.css') }}">


    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <!-- END: Custom CSS-->

    <style>
        .small,
        small {
            font-size: 80%;
            color: red;
        }

    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body
    class="vertical-layout vertical-menu-modern semi-dark-layout 1-column  navbar-sticky footer-static  blank-page blank-page"
    data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="semi-dark-layout">


    <section id="horizontal-vertical">
        <div class="content-body">
            <h3 class="text-center pt-3">{{ config('app.name') }} Analytics Dashboard</h3>
            <div class="row mt-4 mx-1">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bxs-school font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Shops</div>
                                        <h3 class="mb-0">{{ $total_shops ?? null }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                            <i class="bx bx-briefcase-alt font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Products</div>
                                        <h3 class="mb-0">{{ $total_products ?? null }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bx-credit-card font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Orders</div>
                                        <h3 class="mb-0">{{ $orders_delivered ?? null }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                            <i class="bx bxs-wallet font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Shop Earnings</div>
                                        <h3 class="mb-0">{{ $tot_shop_earnings ?? null }} ₹</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                            <i class="bx bx-street-view font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Delivery Boys</div>
                                        <h3 class="mb-0">{{ $total_del_boys ?? null }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bxs-briefcase-alt-2 font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Delivery Charges</div>
                                        <h3 class="mb-0">{{ $total_del_charges ?? null }} ₹</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                            <i class="bx bxs-wrench font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Discounts</div>
                                        <h3 class="mb-0">{{ $total_discounts ?? null }} ₹</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bxs-wallet-alt font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Commission Earnings</div>
                                        <h3 class="mb-0">{{ $total_commissions ?? null }} ₹</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                            <i class="bx bx-user font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Overall Revenue</div>
                                        <h3 class="mb-0">{{ $orders_revenue ?? null }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body py-1">
                                        <div
                                            class="badge-circle badge-circle-lg badge-circle-light-success mx-auto mb-50">
                                            <i class="bx bx-money font-medium-5"></i>
                                        </div>
                                        <div class="text-muted line-ellipsis">Total Vdeliverz Revenue</div>
                                        <h3 class="mb-0">{{ $tot_vdel_earnings ?? null }} ₹</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Column Chart -->
                <div class="col-md-8 col-8 order-summary border-right pr-md-0">
                    <div class="card mb-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Orders Delivered Count</h4>
                            <div class="d-flex">
                                {{-- <button type="button" class="btn btn-sm btn-light-danger mr-1">Cancelled</button>
                                <button type="button" class="btn btn-sm btn-primary glow">Delivered</button> --}}
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body p-0">
                                <div id="order-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-4">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body donut-chart-wrapper">
                                <div id="percentage-pie-chart" class="d-flex justify-content-center"></div>
                                <ul class="list-inline d-flex justify-content-around mb-0">
                                    <li> <span class="bullet bullet-xs bullet-warning mr-50"></span>Discount
                                    </li>
                                    <li> <span class="bullet bullet-xs bullet-success mr-50"></span>Vdeliverz
                                    </li>
                                    <li> <span class="bullet bullet-xs bullet-primary mr-50"></span>Shop
                                    </li>
                                    <li> <span class="bullet bullet-xs bullet-info mr-50"></span>Charge
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/charts/chart-apex.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/dragula.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.min.js') }}"></script>
    <script>
        function change_year(url, year) {
            var shop = $("#filterByType").val();
            window.location.href = 'analytics/' + year + '/1#monthlyChart'
        }

        function changing_both(url) {
            var month = $("#selectMonth").val();
            var year = $("#selectYear").val();
            window.location.href = url + 'analytics/' + year + '/' + month + '#weeklyChart'
        }
        var e = "#5A8DEE",
            t = "#39DA8A",
            o = "#FF5B5C",
            a = "#FDAC41",
            r = "#00CFDD",
            s = "#E2ECFF",
            i = "#ffeed9",
            l = "#828D99"
        gre = "#39DA8A";
        var w = {
            chart: {
                width: 200,
                type: "donut"
            },
            dataLabels: {
                enabled: !1
            },
            series: [{{ $char_shop_total_commissions }}, {{ $char_tot_del_charge }},
                {{ $char_total_discounts }}, {{ $char_total_commissions }}
            ],
            labels: ["Shop", "Charge", "Discount", "Vdeliverz"],
            stroke: {
                width: 0,
                lineCap: "round"
            },
            colors: [e, r, a, gre],
            plotOptions: {
                pie: {
                    donut: {
                        size: "75%",
                        labels: {
                            show: !0,
                            name: {
                                show: !0,
                                fontSize: "15px",
                                colors: "#596778",
                                offsetY: 20,
                                fontFamily: "IBM Plex Sans",
                            },
                            value: {
                                show: !0,
                                fontSize: "26px",
                                fontFamily: "Rubik",
                                color: "#475f7b",
                                offsetY: -20,
                                formatter: function(e) {
                                    return e;
                                },
                            },
                            total: {
                                show: !0,
                                label: "Revenue",
                                color: l,
                                formatter: function(e) {
                                    return e.globals.seriesTotals.reduce(function(
                                            e,
                                            t
                                        ) {
                                            return e + t;
                                        },
                                        0);
                                },
                            },
                        },
                    },
                },
            },
            legend: {
                show: !1
            },
        };
        // new ApexCharts(document.querySelector("#donut-chart"), w).render();
        var y = {
            chart: {
                type: "donut",
                height: 320
            },
            colors: ["#5A8DEE", "#00CFDD", "#FDAC41", '#39DA8A'],
            series: [{{ $char_shop_total_commissions }}, {{ $char_tot_del_charge }},
                {{ $char_total_discounts }}, {{ $char_total_commissions }}
            ],
            legend: {
                itemMargin: {
                    horizontal: 2
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: "bottom"
                    },
                },
            }, ],
        };
        new ApexCharts(document.querySelector("#percentage-pie-chart"), y).render();


        var l = {
            chart: {
                height: 270,
                type: "line",
                stacked: !1,
                toolbar: {
                    show: !1
                },
                sparkline: {
                    enabled: !0
                },
            },
            colors: [e],
            dataLabels: {
                enabled: !1
            },
            stroke: {
                curve: "smooth",
                width: 2.5,
                dashArray: [0, 8]
            },
            fill: {
                type: "gradient",
                gradient: {
                    inverseColors: !1,
                    shade: "light",
                    type: "vertical",
                    gradientToColors: ["#E2ECFF"],
                    opacityFrom: 0.7,
                    opacityTo: 0.55,
                    stops: [0, 80, 100],
                },
            },
            series: [{
                    name: "Delivered",
                    data: {{ json_encode($total_orders) }},
                    type: "area",
                },
                // {
                //     name: "Cancelled",
                //     data: {{ json_encode($cancelled_orders) }},
                //     type: "line",
                // },
            ],
            xaxis: {
                offsetY: -50,
                categories: {{ json_encode($dates) }},
                axisBorder: {
                    show: !1
                },
                axisTicks: {
                    show: !1
                },
                labels: {
                    show: !0,
                    style: {
                        colors: e
                    }
                },
            },
            tooltip: {
                x: {
                    show: !1
                }
            },
        };
        new ApexCharts(document.querySelector("#order-chart"), l).render();
    </script>
</body>

</html>
