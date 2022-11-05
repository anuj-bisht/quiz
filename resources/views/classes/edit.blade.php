@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Class')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            
            {!! Form::model($classes, ['action' => ['ClassesController@update', $classes->id], 'method' => 'PUT']) !!}

            @csrf
            <div class="row">

               
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('class_name', $classes->class_name, array('placeholder' => 'Class Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],$classes->status, array('placeholder' => 'Status','class' => 'form-control')) !!}
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
