@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Reply')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            
            
            {!! Form::model($contactus, ['action' => ['ContactusController@update', $contactus->id], 'method' => 'PUT']) !!}
            @csrf
            <div class="row">

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{$contactus->name}}
                    </div>
                </div>

                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Email:</strong>
                        {{$contactus->email}}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Phone:</strong>
                        {{$contactus->phone}}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Message:</strong>
                        {{$contactus->message}}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Your Response:</strong>
                        <textarea class="form-control" id="body" name="message" required  rows="5" placeholder="Description"></textarea>                        
                    </div>
                </div>
                
                                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    
                    <button type="submit" class="btn btn-primary">Reply</button>
                </div>
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection
