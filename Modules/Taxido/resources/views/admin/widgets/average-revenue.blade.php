       @php
           $commissions = getMonthlyCommissions();
           $adminCommission = $commissions['admin_commission'];
           $driverCommission = $commissions['driver_commission'];
       @endphp
       <div class="col-xxl-7">
           <div class="card">
               <div class="card-header card-no-border">
                   <div class="header-top">
                       <div>
                           <h5 class="m-0">{{ __('taxido::static.widget.average_revenue') }}</h5>
                       </div>
                       <div class="card-header-right-icon">

                       </div>
                   </div>
               </div>
               <div class="card-body pt-0 position-relative">

                   <div class="average-revenue">
                       <div id="average"></div>
                   </div>
               </div>
           </div>
       </div>

       @push('scripts')
           <script src="{{ asset('js/apex-chart.js') }}"></script>
           <script src="{{ asset('js/custom-apexchart.js') }}"></script>
           <script>
               const adminCommission = <?php echo json_encode(array_values($adminCommission)); ?>;
               const driverCommission = <?php echo json_encode(array_values($driverCommission)); ?>;
            
               const chartData = {
                   categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Months
                   series: [{
                           name: 'Admin Commission',
                           data: adminCommission 
                       },
                       {
                           name: 'Driver Commission',
                           data: driverCommission 
                       }
                   ]
               };

               var options = {
                   chart: {
                       type: 'area',
                       height: 410,
                       stacked: false,
                       toolbar: {
                           show: false,
                           tools: {
                               download: false,
                           }
                       },
                       animations: {
                           enabled: true
                       },
                   },
                   legend: {
                       show: false,
                   },
                   dataLabels: {
                       enabled: false,
                   },
                   grid: {
                       strokeDashArray: 3,
                       position: "back",
                       row: {
                           opacity: 0.5,
                       },
                       column: {
                           opacity: 0.5,
                       },
                   },
                   series: chartData.series,
                   xaxis: {
                       categories: chartData.categories, // Use dynamic categories
                       labels: {
                           style: {
                               fontSize: '14px',
                               fontFamily: 'Outfit, sans-serif',
                               fontWeight: 500,
                               colors: '#8D8D8D',
                           },
                       },
                       axisBorder: {
                           show: false,
                       },
                   },
                   yaxis: {
                       labels: {
                           style: {
                               fontSize: '14px',
                               fontFamily: 'Outfit, sans-serif',
                               fontWeight: 500,
                               colors: '#3D434A',
                           },
                       },
                   },
                   fill: {
                       type: 'gradient',
                       gradient: {
                           shadeIntensity: 1,
                           opacityFrom: 0.7,
                           opacityTo: 0.3
                       }
                   },
                   colors: ['#199675', '#ECB238'],
                   stroke: {
                       curve: 'smooth',
                       width: 2
                   },
                   tooltip: {
                       shared: true,
                       y: {
                           formatter: function(value) {
                               return value.toFixed(1); // Display precise values
                           }
                       }
                   },
                   responsive: [{
                       breakpoint: 1400,
                       options: {
                           chart: {
                               height: 300
                           },
                       },
                   }],
               };

               var chart = new ApexCharts(document.querySelector("#average"), options);
               chart.render();
           </script>
       @endpush
