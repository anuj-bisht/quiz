@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create Block')}}</h1>                        
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
                        {!! Form::select('district_id', $districts,[], array('placeholder' => 'District Name','class' => 'form-control')) !!}
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
