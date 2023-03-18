@extends('layouts.main')

@section('content')
    <section class="users-list-wrapper">
<meta name="csrf-token" content="{{ csrf_token() }}" />
        <?php $route = explode('.', Route::currentRouteName()); ?>
        <div class="users-list-table">
            <div class="card">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.0/jquery.js"></script> 
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <!-- datatable start -->
                        <div class="table-responsive">
                            <table id="users-list-datatable" class="table zero-configuration">
                                <thead>
                                    <tr>
                                        <th>Order Id</th>
                                        <th>Pick-Up Data</th>
                                        <th>Pick-Up Time</th>
                                        <th>Pick-Up Mobile</th>
                                        <th>Pick-Up Address</th>
                                        <th>Item Name</th>
                                        <th>Item Detail</th>
                                        <th>Drop Mobile</th>
                                        <th>Drop Address</th>
                                        <th>Total</th>
                                        <th>Order Status</th>
                                        <th>Delivery Boy</th>
                                        <th>Assign</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if (isset($orders) && !empty($orders))
                                        @foreach ($orders as $k => $item)
                                            <tr>
                                                <td>{{ $item->order_id }}</td>
                                                <td>{{ $item->pickup_date }}</td>
                                                <td>{{ $item->pickup_time }}</td>
                                                <td>{{ $item->pickup_mobile }}</td>
                                                <td>{{ $item->pickup_address }}</td>
                                                <td>{{ $item->item_name }}</td>
                                                <td>{{ $item->item_detail }}</td>
                                                <td>{{ $item->droupup_mobile }}</td>
                                                <td>{{ $item->drop_address }}</td>
                                                <td>{{ $item->total }}</td>
                                                <td> {{ $item->order_status }}</td>
                                                <td>{{ $item->delivery_boyDtl }}</td>
@if($item->delivery_boy == '0') <td>   <button type="submit" class="btn btn-danger"  onclick="assign_deliveryBoy({{$item->order_id}},{{$item->latitude}},{{$item->longitude}})">Assign</button>   </td> @endif
                                                               
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                              <div class="mx-auto" style="width: fit-content">{{ $orders->links() }}</div>
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
                
                <div class="modal col-md-12" tabindex="-1" role="dialog" id="personsModal">
                           <div class="modal-dialog" role="document" style="max-width:1000px !important;">
                              <div class="modal-content">
                                 <div class="alert alert-danger" style="display:none"></div>
                                 <div class="modal-header">
                                    <h5 class="modal-title">Assign Delivery Boy</h5>
                                    <button type="button" class="close" id="close_person" name="close_person" data-dismiss="modal" aria-label="person_Close">
                                    <span aria-hidden="true">&times;</span>
                                    <input type="hidden" id="hidorder_id" name="hidorder_id" value="" />
                                    </button>
                                 </div>
                                 <div class="modal-body">
                                    <div class="row">
                                       <div class="form-group col-md-12">
                                                <label for="tab1">Filter Options</label>         
                                                <select name="vehicleType" class="form-control input-lg dynamic" id="vehicleType" 
                                                onchange="getDeliveryBoyByType()">
                                                   <option value="">Select Vehicle Type</option>
                                                  @forelse ($vehicleType as $vehicle)
                                                  <option  value="{{ $vehicle->id}} {{ request()->type == $vehicle->id ? 'selected' : '' }}" >
                                                    {{ $vehicle->transportation_name }}</option>
                                                  @empty
                                            
                                                  @endforelse
                                              </select>  
                                              
                                        <label>Select Delivery Boy</label>
                                         <select data-placeholder="Select Delivery Boy..." name="deliveryBoyId" width="100%" onchange="getDeliveryBoy_deliveryPendingDtls()"
                                                                                    class="select2-icons form-control select2-hidden-accessible"
                                                                                    id="deliveryBoyId" tabindex="-1" aria-hidden="true">
                                             
                                              <option value="0">Select Delivery Boy</option>
                                        </select>
                                       </div>
                                       <div id="pending_orders">
                                           <h3>Pending Orders</h3>
                                           <table id="records_table" border='1' class="table zero-configuration">
                                                <tr>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                 </div>
                                 <div class="modal-footer">
                                    <button  class="btn btn-success" id="ajaxSubmit" onclick="DeliveryBoyAdd()">Assign Delivery Boy</button>
                                 </div>
                              </div>
                           </div>
                        </div>
            </div>
        </div>
        
          <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

            <script type="text/javascript">
            
                function show_addVehiclemodalpopup(){
                $('#addVehicleModal').modal('show');
                }
            
                $("#close_addVehicleModal").click(function(){
                
                    $("#addVehicleModal").modal('hide'); 
                    
                });
                
                $("#close_person").click(function(){
                
                     $("#personsModal").modal('hide'); 
                     
                });
            
                function assign_deliveryBoy(order_id,latitude,longitude)
                { 
                    $('#hidorder_id').val(order_id);
                    //alert($('#hidorder_id').val());
                     $.ajax({
                       type:'POST',
                       url:"{{ route('get_nearest_deliveryBoy') }}",
                       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                       data:{db_latitude:latitude,db_longitude:longitude},
                       success: function(data) {
                            
                           // $('#deliveryBoyId').html(data.html);
                            $('#pending_orders').hide();
                            $('#personsModal').modal('show');
                       }
                    });
                }
            
            
                function getDeliveryBoyByType()
                {
                    var vehicle_type = $( "#vehicleType option:selected" ).text();
                    
                     $.ajax({
                       type:'POST',
                       url:"{{ route('getdelivery_boyBy_type') }}",
                       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                       data:{vehicle_type:vehicle_type},
                       success: function(data) {
                            
                            $('#deliveryBoyId').html(data.html);
                            $('#pending_orders').hide();
                            $('#personsModal').modal('show');
                       }
                    });
                }
            
                function getDeliveryBoy_deliveryPendingDtls()
                {
                    var delivery_boyId = $('#deliveryBoyId').val();
                    $.ajax({
                       type:'POST',
                       url:"{{ route('getDeliveryboy_pendingOrders') }}",
                       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                       data:{delivery_boyId:delivery_boyId},
                       success: function(response) {
                           if(response.data.length > 0)
                           {
                                var tableData = response.data;
                                var trHTML = '<tr> <th>Order Id</th><th>Pickup Date</th><th>Pickup Time</th><th>Pickup Address</th><th>Item Name</th><th>Item Detail</th><th>Drop Address</th></tr>';
                                $.each(tableData, function (i, item) {
                                    trHTML += '<tr><td>' + item.order_id + '</td><td>' + item.pickup_date + '</td><td>' + item.pickup_time + '</td><td>' 
                                    + item.pickup_address + '</td><td>' + item.item_name + '</td><td>' + item.item_detail + '</td><td>' + item.drop_address + '</td></tr>';
                                });
                                $('#records_table').append(trHTML);
                                $('#pending_orders').show();
                           }
                           else
                           {
                               $('#records_table').empty();
                               $('#pending_orders').hide();
                           }
                       }
                    });
                }
                
                function DeliveryBoyAdd()
                {
                $.ajax({
                   type:'POST',
                   url:"{{ route('assign_deliveryBoy') }}",
                   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                   data:{order_id:$('#hidorder_id').val(),delivery_boyId:$('#deliveryBoyId').val(),order_stat:'2'},
                   success: function(data) {
                        alert('DeliveryBoy assigned successfully.');
                        window.location.reload();
                   }
                });
                
                }
            </script>
    </section>
@endsection
