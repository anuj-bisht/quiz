@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Plan')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            
            {!! Form::model($plan, ['action' => ['PlanController@update', $plan->id], 'method' => 'PUT', 'files'=>true]) !!}

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
		    <input type="hidden" value="{{$plan->image}}" class="form-control" name="image1">
                </div>
		<div class="form-group col-xs-12 col-sm-12 col-md-12">
                    <label>Image</label><br>
                    <td><img src="{{asset($plan->image)}}" alt="" width="100px" height="100px"></td>
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
