@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create Exam')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            @php
                $route = $view_name.'.store';
            @endphp
            <form method="POST" id="questionForm" action='{{ route("$route") }}' enctype="multipart/form-data">            
            
            @csrf
            <div class="row">
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Class:</strong>
                        {!! Form::select('class_id',$classes, null, array('placeholder' => 'Class','id'=>'class_id','class' => 'form-control')) !!}
                    </div>
                </div>

		<div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Exam Type:</strong>
                        {!! Form::select('exam_type', ["Subject"=>'Subject',"Chapter"=>"Chapter"],[], array('placeholder' => 'Exam Type','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Subject:</strong>
                        {!! Form::select('subject_id',[], null, array('placeholder' => 'Subject','id'=>'subject_id','class' => 'form-control')) !!}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Chapter:</strong>
                        {!! Form::select('chapter_id[]',[], null, array('placeholder' => 'Chapter','id'=>'chapter_id','class' => 'form-control','multiple'=>'true')) !!}
                    </div>
                </div>
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Title:</strong>
                        {!! Form::text('title', null, array('placeholder' => 'Exam Title','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>description:</strong>
                        {!! Form::textarea('description',null, array('placeholder' => 'Exam description','class' => 'form-control ckeditor')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Exam Duration:</strong>                        
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Hour:</strong>    
                        <select name="hour" id="hour" class="form-control">
                            <option value="0">Hour</option>
                            @for($i=0;$i<=6;$i++)                    
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-6">
                    <div class="form-group">
                        <strong>Minute:</strong>    
                        <select name="minute" id="minute" class="form-control">
                            <option value="0">Minute</option>
                            @for($i=0;$i<=60;$i++)                    
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                        
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>No of Question:</strong>
                        {!! Form::text('no_of_question', null, array('placeholder' => 'No of question','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Max Marks:</strong>
                        {!! Form::text('max_marks', null, array('placeholder' => 'Max Marks','class' => 'form-control')) !!}
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

<script type="text/javascript">
    
</script>
@endsection
