@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Plan')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            <form method="POST" action='{{ route("plans.store") }}' enctype="multipart/form-data">

            @csrf
            <div class="row">
                

                     <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            {!! Form::text('plan_name', null, array('placeholder' => 'Plan Name' , 'class' => 'form-control')) !!}
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>description:</strong>
                            {!! Form::textarea('description', null, array('placeholder' => 'Plan description','class' => 'ckeditor form-control')) !!}
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>No Of Days:</strong>
                            {!! Form::text('days', null, array('placeholder' => 'days','class' => 'form-control')) !!}
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Plan Rate:</strong>
                            {!! Form::text('plan_rate', null, array('placeholder' => 'Plan Rate','class' => 'form-control')) !!}
                        </div>
                    </div>
		    <div class="form-group col-xs-12 col-sm-12 col-md-12">
                    	<label>Image</label><br>
                    	<input type="file" class="form-control" name="image">
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
