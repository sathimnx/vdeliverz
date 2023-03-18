@extends('layouts.main')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
    <section class="users-list-wrapper"><style>
   /*
   CSS for the main interaction
   */
   .tabset > input[type="radio"] {
   position: absolute;
   left: -200vw;
   }
   .tabset .tab-panel {
   display: none;
   }
   .tabset > input:first-child:checked ~ .tab-panels > .tab-panel:first-child,
   .tabset > input:nth-child(3):checked ~ .tab-panels > .tab-panel:nth-child(2),
   .tabset > input:nth-child(5):checked ~ .tab-panels > .tab-panel:nth-child(3),
   .tabset > input:nth-child(7):checked ~ .tab-panels > .tab-panel:nth-child(4),
   .tabset > input:nth-child(9):checked ~ .tab-panels > .tab-panel:nth-child(5),
   .tabset > input:nth-child(11):checked ~ .tab-panels > .tab-panel:nth-child(6) {
   display: block;
   }
   /*
   Styling
   */
   .tabset > label {
   position: relative;
   display: inline-block;
   padding: 15px 15px 25px;
   border: 1px solid transparent;
   border-bottom: 0;
   cursor: pointer;
   font-weight: 600;
   }
   .tabset > label::after {
   content: "";
   position: absolute;
   left: 15px;
   bottom: 10px;
   width: 22px;
   height: 4px;
   background: #8d8d8d;
   }
   .tabset > label:hover,
   .tabset > input:focus + label {
   color: #06c;
   }
   .tabset > label:hover::after,
   .tabset > input:focus + label::after,
   .tabset > input:checked + label::after {
   background: #06c;
   }
   .tabset > input:checked + label {
   border-color: #ccc;
   border-bottom: 1px solid #fff;
   margin-bottom: -1px;
   }
   .tab-panel {
   padding: 30px 0;
   border-top: 1px solid #ccc;
   }
   /*
   Demo purposes only
   */
   *,
   *:before,
   *:after {
   box-sizing: border-box;
   }
   .tabset {
   max-width: 65em;
   }
</style>

    <div class="users-list-table">
         <div class="card">
  <div class="card-content">
                        <div class="card-body">
                           <!-- datatable start -->
                           <div class="row">

                                <div class="col-sm-6">
                                      <a onclick="show_addVehiclemodalpopup(0)" class="btn btn-warning float-left" style="color: white;"
                                                 >Add New Vehicle</a>
                                                 <br/><br/>
                                                 <br/>
                                                 </div>
                                                  <div class="col-sm-6">
                                <label for="tab1">Filter Options</label>         
                                <select name="Filter_vehicleType" class="form-control input-lg dynamic" id="Filter_vehicleType" data-dependent="couponCategory" onchange="filterOrder('{{ env('APP_URL') }}')">
                                   <option value="">Select Vehicle Type</option>
                                   <option value="0">All Vehicles</option>
                                  @forelse ($vehicleType as $vehicle)
                                  <option  value="{{ $vehicle->id}} {{ request()->type == $vehicle->id ? 'selected' : '' }}" >
                                    {{ $vehicle->transportation_name }}</option>
                                  @empty
                            
                                  @endforelse
                              </select>         
                                </div></div>   
                        </div>
                          @push('scripts')
                                <script>
                                    function filterOrder(url) {
                                        var type = $('#Filter_vehicleType').val();
                                        window.location.href = url + 'charge_filter/' + type ;
                                    }
                                </script>
                            @endpush
                     </div>
                     <div class="card-content">
                        <div class="card-body">
                           <!-- datatable start -->
                           <div class="table-responsive">
                              <table class="table no-bordered" style="width:100%;">
                                 <thead>
                                    <tr>
                                       <th style="text-align: center; vclass="table zero-configuration"ertical-align: middle;">Id</th>
                                       <th style="text-align: center; vertical-align: middle;">Vehicle Type</th>
                                       <th style="text-align: center; vertical-align: middle;">Vehicle Name</th>
                                       <th style="text-align: center; vertical-align: middle;">Vehicle Number</th>
                                       <th style="text-align: center; vertical-align: middle;">Basic Charge</th>
                                       <th style="text-align: center; vertical-align: middle;">Basic KM</th>
                                       <th style="text-align: center; vertical-align: middle;">Extra Charge</th>
                                       @role('admin')
                                       @if(auth()->user()->id == 1)
                                       <th style="text-align: center; vertical-align: middle;">Action</th>
                                       @endif @endrole   
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @if (isset($charges) && !empty($charges))
                                    @foreach ($charges as $key => $item)
                                    <tr>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->id}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->vehicle_type}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->vehicle_name}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->vehicle_number}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->basic_charge}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->basic_km}}</td>
                                       <td style="text-align: center; vertical-align: middle;">{{$item->extra_charge}}</td>
                                      
                                       <td style="text-align: center; vertical-align: middle;">
                                                <button type="submit" class="btn-outline-info" onclick="show_addVehiclemodalpopup({{$item->id}})" data-icon="warning-alt">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                       </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                 </tbody>
                              </table>
                           </div>
                           <div class="modal" tabindex="-1" role="dialog" id="addVehicleModal">
                              <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                    <div class="alert alert-danger" style="display:none"></div>
                                    <div class="modal-header">
                                       <h5 class="modal-title">
                                           Add New Vehicle</h5>
                                       <button type="button" class="close" id="close_addVehicleModal" name="close_addVehicleModal" data-dismiss="modal" aria-label="frame_Close">
                                       <span aria-hidden="true">&times;</span>
                                       </button>
                                    </div>
                                    <div class="modal-body">
                                       <div class="row">
                                          <div class="form-group col-md-12">
                                            <label for="Vehicle_Type">Vehicle Type:</label>
                                            <input type="hidden" class="form-control" name="type" id="type">
                                            <select name="vehicle_type" class="form-control input-lg dynamic" id="vehicle_type" onchange="getTwoWheelercharges()">
                                                 <option value="0">Select Vehicle Type</option>
                                                  @forelse ($vehicleType as $vehicle)
                                                  <option  value="{{ $vehicle->id}} {{ request()->type == $vehicle->id ? 'selected' : '' }}" >
                                                    {{ $vehicle->transportation_name }}</option>
                                                  @empty
                                            
                                                  @endforelse
                                             </select>
                                             
                                             <label for="Vehicle_name">Vehicle Name:</label>
                                             <input type="text" class="form-control" name="vehicle_name" id="vehicle_name" required>
                                             
                                             <label for="Vehicle_name">Vehicle Number:</label>
                                             <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required>
                                             
                                             </hr>
                                             
                                                        
                                            <label for="">Two Wheeler Basic Charge</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.00001" class="form-control" id="dTd_basicCharge"
                                                    name="dTd_basicCharge" placeholder="Basic Charge"
                                                    value="" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipesPreTimede674">Per Basic
                                                        KMS</span>
                                                </div>
                                            </div>
                                
                                        
                                            <label for="">Two Wheeler Basic KM</label>
                                            <div class="input-group">
                                                
                                                <input type="number" step="0.00001" class="form-control" id="dTd_basic_KM"
                                                    name="dTd_basicKM" placeholder="Basic Kilo Meter"
                                                    value="" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674"> 
                                                        KM</span>
                                                </div>
                                            </div>
                                     
                                       
                                            <label for="">Two Wheeler Extra Charge</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"
                                                        id="recipePreTismede674">{{ config('constants.currency') }}</span>
                                                </div>
                                                <input type="number" step="0.00001" class="form-control"id="dTd_extraCharge"
                                                    name="dTd_extraCharge" placeholder="Extra Charge"
                                                    value="" required>
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="recipePreTimede674">Per Extra KM</span>
                                                </div>
                                            </div>
                                       
                                            <input type="hidden" class="form-control" name="charges_id" id="charges_id">
                           
                                          </div>
                                       </div>
                                    </div>
                                    <div class="modal-footer">
                                       <button  class="btn btn-success" id="ajaxSubmit" onclick="save_newvehicle()">Save changes</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div></div></div>
                     </div>
                         <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

     <script type="text/javascript">
     
     $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
     
     function show_addVehiclemodalpopup(chargeId){
         if(chargeId == 0)
         {
             var empty="";
            $('#addVehicleModal').modal('show');
            $('#dTd_basic_KM').val(empty);
            $('#dTd_basicCharge').val(empty);
            $('#dTd_extraCharge').val(empty);
            $('#vehicle_name').val(empty);
            $('#vehicle_number').val(empty);
            $("#vehicle_type").prop("selectedIndex", 0);
            $('#type').val('add');
         }
         else
         {
             $('#type').val('edit');
             $('#charges_id').val(chargeId);
              $.ajax({
               type:'POST',
               url:"{{ route('edit') }}",
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               data:{charge_id:chargeId},
               success: function(data) {
                   var type_id= data.vehicle_type_id;
                    $('#dTd_basic_KM').val(data.basic_km);
                    $('#dTd_basicCharge').val(data.basic_charge);
                    $('#dTd_extraCharge').val(data.extra_charge);
                    $('#vehicle_name').val(data.vehicle_name);
                    $('#vehicle_number').val(data.vehicle_number);
                   //$("#vehicle_type option[value=2]").prop('selected', true);
                   $("#vehicle_type").prop("selectedIndex", type_id);
                   //  $('#vehicle_type option[value="' + data.vehicle_type_id+ '"]').prop('selected', true);
                   // var id = data.vehicle_type_id;
                     //$("#vehicle_type option:selected").text = data.vehicle_type;
                    //$("#vehicle_type").val(data.vehicle_type_id).attr('selected','selected');
                    //$('#vehicle_type option[value="'+2+'"]').prop('selected', true);
                    //$("#vehicle_type option:selected").val = data.vehicle_type_id;
                   // $("#vehicle_type").text(data.vehicle_type).attr('selected',true);
                    //$('#vehicle_type option[value="' + data.vehicle_type_id+ '"]').prop('selected', true);

                    
                    $('#addVehicleModal').modal('show');
               }
           });
             
         }
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
                    
                    $('#deliveryBoyId').html(data.html);
                    $('#personsModal').modal('show');
               }
           });
        }
        
        function DeliveryBoyAdd()
        {
            $.ajax({
               type:'POST',
               url:"{{ route('assign_deliveryBoy') }}",
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
               data:{order_id:$('#hidorder_id').val(),delivery_boyId:$('#deliveryBoyId').val()},
               success: function(data) {
                    alert('DeliveryBoy assigned successfully.');
                    window.location.reload();
               }
           });
            
        }
        
        function getTwoWheelercharges()
        { 
            if($('#type').val() == 'add')
            {
                var vehicle_type = $( "#vehicle_type option:selected" ).text();
                
                if(jQuery.trim(vehicle_type) == 'Two Wheeler')
                {
                     $.ajax({
                       type:'POST',
                       url:"{{ route('get_TwoWheelerCharges') }}",
                       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                       success: function(data) {
                            $('#dTd_basic_KM').val(data.basic_km);
                            $('#dTd_basicCharge').val(data.basic_charge);
                            $('#dTd_extraCharge').val(data.extra_charge);
                       }
                   });
                }
                else
                {
                    var empty="";
                    $('#dTd_basic_KM').val(empty);
                    $('#dTd_basicCharge').val(empty);
                    $('#dTd_extraCharge').val(empty);
                }
            }
        }
        
        function save_newvehicle()
        {
            if($("#vehicle_type").val() == '0')
            {
                 alert("Select Vehicle Type..");
            }
            else
            {
                $.ajax({
                   type:'POST',
                   url:"{{ route('save_newVehicle') }}",
                   headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                   data:{vehicle_type:$("#vehicle_type option:selected").text(),vehicle_type_id:$('#vehicle_type').val(),vehicle_name:$('#vehicle_name').val(),vehicle_number:$('#vehicle_number').val(),
                       dTd_basicCharge:$('#dTd_basicCharge').val(),dTd_basic_KM:$('#dTd_basic_KM').val(),dTd_extraCharge:$('#dTd_extraCharge').val(),type:$('#type').val(),charge_id:$('#charges_id').val()
                   },
                   success: function(data) {
                        alert('Vehicles data added successfully.');
                        window.location.reload();
                   }
               });
            }
        }
        
           </script>
    </section>
@endsection