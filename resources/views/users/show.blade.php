@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Show User')}}</h1>                        
                    </div>		                    
            </div>
            
            @include('layouts.flash')
            
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $user->name }}
                    </div>
                </div>
                
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Email:</strong>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Gender:</strong>
                        @if($user->gender=='M')
                            Male
                        @elseif($user->gender=='F')    
                            Female
                        @else
                            Other                            
                        @endif
                        
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Roles:</strong>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <label class="badge badge-success">{{ $v }}</label>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Age:</strong>
                        {{ $user->age }}
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Phone:</strong>
                        {{ $user->phone }}
                    </div>
                </div>

                
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Diet Type:</strong>
                        {{ $user->diet_type }}
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Initial Weight:</strong>
                        {{ $user->initial_weight }}
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Goal Weight:</strong>
                        {{ $user->goal_weight }}
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Image:</strong>
                        @if(file_exists($user->image))
                            <img src="{{$user->image}}" alt="" style="max-width:200px;max-height:200px">
                        @else
                            <img src="{{url('/')}}/images/noimage.png" alt="" style="max-width:50px;max-height:50px">
                        @endif
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Front Image:</strong>
                        @if(file_exists($user->image_front))
                            <img src="{{$user->image_front}}" alt="" style="max-width:200px;max-height:200px">
                        @else
                            <img src="{{url('/')}}/images/noimage.png" alt="" style="max-width:50px;max-height:50px">
                        @endif
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Back Image:</strong>
                        @if(file_exists($user->image_back))
                            <img src="{{$user->image_back}}" alt="" style="max-width:200px;max-height:200px">
                        @else
                            <img src="{{url('/')}}/images/noimage.png" alt="" style="max-width:50px;max-height:50px">
                        @endif
                    </div>
                </div>

                
                <div class="row">
                        <div class="col-lg-10">
                            <h1 class="page-header">{{__('Subscription List')}}</h1>                        
                        </div>		                    
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
                    <thead>
                        <tr>               
                        <th>Category</th>              
                        <th>Plan</th>
                        <th>Price</th>
                        <th>Diet Type</th>
                        <th>Plan Type</th>
                        <th>Start Date</th>              
                        <th>Nex Bill Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($subscription))
                          @foreach($subscription as $k=>$v)   
                            <tr>
                                <td>{{$v->category_name}}</td>
                                <td>{{$v->plan_name}}</td>
                                <td>{{$v->plan_price}}</td>
                                <td>{{$v->diet_type}}</td>
                                <td>{{$v->client_type}}</td>
                                <td>{{$v->subscription_start_date}}</td>
                                <td>{{$v->next_bill_date}}</td>
                            </tr>    
                          @endforeach   
                    @else
                            <tr>
                                <td colspan="7"><i>No Record Found</i></td>
                            </tr>
                    @endif
                    </tbody>
                    <tfoot>
                        <tr>               
                            <th>Category</th>              
                            <th>Plan</th>
                            <th>Price</th>
                            <th>Diet Type</th>
                            <th>Plan Type</th>
                            <th>Start Date</th>     
                            <th>Nex Bill Date</th>         
                       
                        </tr>
                    </tfoot>
                </table>  

                <div class="row">
                        <div class="col-lg-10">
                            <h1 class="page-header">{{__('Schedule List')}}</h1>                        
                        </div>		                    
                </div>
                <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
                    <thead>
                        <tr>               
                            <th>Category</th>              
                            <th>Start</th>
                            <th>End</th>
                            <th>Slot name</th>
                            <th>Trainer</th>
                            <th>Reschedule Status</th>              
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($schedule))
                          @foreach($schedule as $k=>$v)   
                            <tr>
                                <td>{{$v->category_name}}</td>
                                <td>{{$v->start_time}}</td>
                                <td>{{$v->end_time}}</td>
                                <td>{{$v->slot_name}}</td>
                                <td>{{$v->trainer_name}}</td>
                                <td>
                                @if($v->reschedule_status == 'Y')
                                    Yes
                                @else
                                    No
                                @endif
                                    
                                </td>
                            </tr>    
                          @endforeach   
                    @else
                            <tr>
                                <td colspan="7"><i>No Record Found</i></td>
                            </tr>
                    @endif
                    </tbody>
                    <tfoot>
                        <tr>               
                            <th>Category</th>              
                            <th>Start</th>
                            <th>End</th>
                            <th>Slote</th>
                            <th>Trainer</th>
                            <th>Reschedule Status</th>              
                        </tr>
                    </tfoot>
                </table>  

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
                </div>                
            </div>
                        
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection