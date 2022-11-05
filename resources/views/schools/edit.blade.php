@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit School')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            

            {!! Form::model($school, ['action' => ['SchoolController@update', $school->id], 'method' => 'PUT']) !!}
        
            @csrf
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select District:</strong>
                        {!! Form::select('district_id', $districts,$school->district_id, array('placeholder' => 'District Name','id'=>'district_id','class' => 'form-control')) !!}
                    </div>
                </div>

                <!-- <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select Block:</strong>
                        {!! Form::select('block_id', $blocks,$school->block_id, array('placeholder' => 'Block Name','id'=>'block_id','class' => 'form-control')) !!}
                    </div>
                </div> -->

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} Name:</strong>
                        {!! Form::text('school_name', $school->school_name, array('placeholder' => $ctrl_name.' Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} code:</strong>
                        {!! Form::text('school_code', $school->school_code, array('placeholder' => $ctrl_name.' Code','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} pincode:</strong>
                        {!! Form::text('school_pincode', $school->school_pincode, array('placeholder' => $ctrl_name.' pinode','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} address:</strong>
                        {!! Form::textarea('school_address', $school->school_address, array('placeholder' => $ctrl_name.' Address','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],$school->status, array('placeholder' => 'Status','class' => 'form-control')) !!}
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
