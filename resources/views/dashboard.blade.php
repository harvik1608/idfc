@extends('include.header')
@section('content')
<div class="welcome-wrap mb-4">
    <div class=" d-flex align-items-center justify-content-between flex-wrap">
        <div class="mb-3">
            <h2 class="mb-1 text-white">Hello {{ Auth::user()->name }}</h2>
            <p class="text-light"><b>Welcome to {{ config('constant.app_name') }} - {{ date('d M, Y') }}</b></p>
        </div>
    </div>
    <div class="welcome-bg">
        <img src="{{ asset('assets/img/welcome-bg-02.svg') }}" alt="img" class="welcome-bg-01">
        <img src="{{ asset('assets/img/welcome-bg-01.svg') }}" alt="img" class="welcome-bg-03">
    </div>
</div>
<div class="row">
    <div class="col-lg-12 d-flex">
        <div class="card flex-fill">
            <div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
                <h5 class="mb-2">Top 10 Cities</h5>                               
                <!-- <div class="dropdown mb-2">
                    <a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-calendar me-1"></i>2025
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2024</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2025</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">2023</a>
                        </li>
                    </ul>
                </div> -->
            </div>
            <div class="card-body pb-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <!-- <div class="mb-1">
                        <h5 class="mb-1">$45787</h5>
                        <p><span class="text-success fw-bold">+40%</span> increased from last year</p>
                    </div>
                    <p class="fs-13 text-gray-9 d-flex align-items-center mb-1"><i class="ti ti-circle-filled me-1 fs-6 text-primary"></i>Revenue</p> -->
                </div>
                <div id="revenue-income"></div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script>
	var page_title = "Dashboard";
    var all_cities  = @json($all_cities);
    var city_counts = @json($city_counts);
    $(document).ready(function(){
        customer_chart();
    });
    function customer_chart()
    {
        if ($('#revenue-income').length > 0) {
            var sColStacked = {
                chart: {
                    height: 400,
                    type: 'bar',
                    stacked: true,
                    toolbar: {
                        show: false,
                    },
                    fontFamily: "Nunito"
                },
                colors: ['#9c1d26', '#F8F9FA'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }],
                plotOptions: {
                    bar: {
                        borderRadius: 5, 
                        borderRadiusWhenStacked: 'all',
                        horizontal: false,
                        endingShape: 'rounded'
                    },
                },
                series: [{
                    name: 'Customer',
                    data: city_counts
                }],
                xaxis: {
                    categories: all_cities,
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '13px',
                        }
                    }
                },
                grid: {
                    borderColor: 'transparent',
                    strokeDashArray: 5,
                    padding: {
                        left: -8,
                    },
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false 
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return  val;
                        }
                    }
                },
                dataLabels: {
                    enabled: true,  // Enable labels
                    formatter: function(val) { return val; }, // Display the number
                    offsetY: -3, // Position above the bar
                    style: {
                        fontSize: '15px',
                        colors: ['#ffffff'] // Label color
                    }
                },
                tooltip: {
                    y: { formatter: function(val) { return val; } }
                },
                fill: { opacity: 1 }
            }
            var chart = new ApexCharts(document.querySelector("#revenue-income"),sColStacked);
            chart.render();
        }
    }
</script>
@endsection
