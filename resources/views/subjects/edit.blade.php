@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit '.$ctrl_name)}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            
            {!! Form::model($subject, ['action' => ['SubjectController@update', $subject->id], 'method' => 'PUT','enctype'=>'multipart/form-data']) !!}

            @csrf
            <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Class:</strong>
                        {!! Form::select('class_id',$classes,null, array('placeholder' => 'Classes','class' => 'form-control')) !!}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject Name:</strong>
                        {!! Form::text('subject_name', null, array('placeholder' => 'Subject Name','class' => 'form-control')) !!}
                    </div>
                </div>

                @if(file_exists($subject->subject_banner_path))
                <div class="form-group">                        
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <img src="{{$subject->subject_banner}}" style="max-width:200px; max-height:200px">
                </div>
                </div>  
                @endif 
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject Banner:</strong>
                        <input type="file" class="form-control" name="subject_banner">
                    </div>
                </div>

                @if(file_exists($subject->subject_logo_path))
                <div class="form-group">                        
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <img src="{{$subject->subject_logo}}" style="max-width:200px; max-height:200px">
                </div>
                </div>  
                @endif 

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject Logo:</strong>
                        <input type="file" class="form-control" name="subject_logo">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],null, array('placeholder' => 'Status','class' => 'form-control')) !!}
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
