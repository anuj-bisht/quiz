@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create '.$ctrl_name)}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            @php
                $route = $view_name.'.store';
            @endphp
            <form method="POST" action='{{ route("$route") }}'>

            @csrf
            <div class="row">
                
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select District:</strong>
                        {!! Form::select('district_id', $districts,[], array('placeholder' => 'District Name','id'=>'district_id','class' => 'form-control')) !!}
                    </div>
                </div>

                <!-- <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select Block:</strong>
                        {!! Form::select('block_id', [],[], array('placeholder' => 'Block Name','id'=>'block_id','class' => 'form-control')) !!}
                    </div>
                </div> -->

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} Name:</strong>
                        {!! Form::text('school_name', null, array('placeholder' => $ctrl_name.' Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} code:</strong>
                        {!! Form::text('school_code', null, array('placeholder' => $ctrl_name.' Code','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} pincode:</strong>
                        {!! Form::text('school_pincode', null, array('placeholder' => $ctrl_name.' pinode','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} address:</strong>
                        {!! Form::textarea('school_address', null, array('placeholder' => $ctrl_name.' Address','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],[], array('placeholder' => 'Status','class' => 'form-control')) !!}
                    </div>
                </div>
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            </form>
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
