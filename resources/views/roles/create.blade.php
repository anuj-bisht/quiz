@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Role')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
            

            <div class="row">
            
                <div class="col-md-6">
                    
                        <div class="form-group">
                            <strong>Name:</strong>
                            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                        </div>
                    
                    
                        <div class="form-group">
                            <strong>Permission:</strong>
                            <br/>
                            @foreach($permission as $value)
                            <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                            {{ $value->name }}</label>
                        <br/>
                        @endforeach
                        </div>
                    
                </div>

                
             </div>

             <div class="row">   
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('roles.index') }}"> Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            {!! Form::close() !!}                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
