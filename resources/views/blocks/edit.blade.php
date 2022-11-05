@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Block')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            

            {!! Form::model($block, ['action' => ['BlockController@update', $block->id], 'method' => 'PUT']) !!}
        
            @csrf
            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Select District:</strong>
                        {!! Form::select('district_id', $districts,$block->district_id, array('placeholder' => 'District Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} Name:</strong>
                        {!! Form::text('block_name', null, array('placeholder' => $ctrl_name.' Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{$ctrl_name}} code:</strong>
                        {!! Form::text('block_code', null, array('placeholder' => $ctrl_name.' Code','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],$block->status, array('placeholder' => 'Status','class' => 'form-control')) !!}
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
