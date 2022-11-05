@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit district')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            

            {!! Form::model($district, ['action' => ['DistrictController@update', $district->id], 'method' => 'PUT']) !!}
        
            @csrf
            <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} Name:</strong>
                        {!! Form::text('district_name', $district->district_name, array('placeholder' => $ctrl_name.' Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} code:</strong>
                        {!! Form::text('district_code', $district->district_code, array('placeholder' => $ctrl_name.' Code','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],$district->status, array('placeholder' => 'Status','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
