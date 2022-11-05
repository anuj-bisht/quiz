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

            
            {!! Form::model($chapter, ['action' => ['ChapterController@update', $chapter->id], 'method' => 'PUT']) !!}

            @csrf
            <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Classes:</strong>
                        {!! Form::select('class_id',$classes, null, array('placeholder' => 'Classes','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject:</strong>
                        {!! Form::select('subject_id',$subjects, null, array('placeholder' => 'Subject','class' => 'form-control')) !!}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Chapter Name English:</strong>
                        {!! Form::text('chapter_name', null, array('placeholder' => 'Chapter Name','class' => 'form-control')) !!}
                    </div>
                </div>

		<div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Chapter Name Hindi:</strong>
                        {!! Form::text('chapter_name_hindi', null, array('placeholder' => 'Chapter Name','class' => 'form-control')) !!}
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
