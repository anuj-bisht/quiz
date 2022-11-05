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

            
            
            {!! Form::model($question, ['action' => ['QuestionController@update', $question->id], 'method' => 'PUT','id'=>'questionForm']) !!}

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
                        <strong>Subject:</strong>
                        {!! Form::select('subject_id',$subjects, null, array('placeholder' => 'Subject','id'=>'subject_id','class' => 'form-control')) !!}
                    </div>
                </div>

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Chapter:</strong>
                        {!! Form::select('chapter_id',$chapters, null, array('placeholder' => 'Chapter','id'=>'chapter_id','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Language:</strong>
                        {!! Form::select('language_id',$languages, null, array('placeholder' => 'Chapter','id'=>'chapter_id','class' => 'form-control')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Is Convert:</strong>
                        <input type="checkbox" name="lang_convert" value="Yes" @if($question->lang_convert == "Yes") checked="checked" @endif >
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Status:</strong>
                        {!! Form::select('status', ["Y"=>'Active',"N"=>"De-active"],null, array('placeholder' => 'Status','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Question Type:</strong>
                        {!! Form::select('is_image',["Y"=>'Image','N'=>'Normal'], null, array('placeholder' => 'Question Type','id'=>'question_type','class' => 'form-control')) !!}
                    </div>
                </div>

                

                <div class="col-xs-12 col-sm-12 col-md-12" id="normal_question">
                    <div class="form-group">
                        <strong>Question:</strong>
                        {!! Form::text('question', null, array('placeholder' => 'Question Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12" id="image_question" style="display:none"> 
                    <div class="form-group">
                        <strong>Question Image:</strong>
                        <input type="file" accept="image/*" id="imgInp" class="form-control" name="file">
                        <img id="blah" src="{{url('/')}}/img/1px.png" alt="your image" style="max-width:250px;max-height:250px"/>
                    </div>
                </div>
                @if(isset($question->option) && $question->option->count()>0)
                    @foreach($question->option as $k => $v)
                    @php
                        $checked = "";
                        if($v->is_correct=='Y'){
                            $checked = "checked";
                        }
                    @endphp
                    <div class="field_wrapper">
                        <div class="col-xs-9 col-sm-9 col-md-9">
                            <div class="form-group">
                                <strong>Option{{$k+1}}:</strong>
                                {!! Form::text('option[]', $v->option_name, array('placeholder' => 'Option1','class' => 'form-control')) !!}
                                {{ Form::hidden('option_id[]', $v->id) }}
                            </div>
                        </div>

                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <div class="form-group">                        
                                {{ Form::checkbox('answer[]',null,$checked, array('class' => 'form-control','id'=>'answer','style'=>'margin-top: 20px;')) }}
                            </div>
                        </div>                    
                    </div>
                    @endforeach
                @endif
		<div class="col-xs-12 col-sm-12 col-md-12" id="normal_question_1">
                    <div class="form-group">
                        <strong>Question Hindi:</strong>
                        <input type="text" class="form-control" name="question_hindi" value="{{$question->question_hindi}}" placeholder="question hindi" />
                    </div>
                </div>    
                <?php $i=-1; $option_hindi1 = ""; $option_hindi2 = ""; $option_hindi3 = ""; $option_hindi4 = "";
		foreach($options as $val){
			$i++;
			if($i==0){ $option_hindi1 = $val->option_hindi; }
			if($i==1){ $option_hindi2 = $val->option_hindi; } 
			if($i==2){ $option_hindi3 = $val->option_hindi; }
			if($i==3){ $option_hindi4 = $val->option_hindi; }
		}
		?>
                <div class="field_wrapper_1">
                    <div class="col-xs-9 col-sm-9 col-md-9">
                        <div class="form-group">
                            <strong>Option1:</strong>
                            <input type="text" class="form-control" name="option_hindi_1" id="option_hindi_1" value="{{$option_hindi1}}" placeholder="option hindi" />
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="form-group">                        
                            <input type="checkbox" class="form-control" name="checked_hindi_1" id="checked_hindi_1"  />
                        </div>
                    </div>

                    <div class="col-xs-9 col-sm-9 col-md-9">
                        <div class="form-group">
                            <strong>Option2:</strong>
                            <input type="text" class="form-control" name="option_hindi_2" id="option_hindi_2" value="{{$option_hindi2}}" placeholder="option hindi" />
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="form-group">                        
                            <input type="checkbox" class="form-control" name="checked_hindi_2" id="checked_hindi_3"  />
                        </div>
                    </div>
                    <div class="col-xs-9 col-sm-9 col-md-9">
                        <div class="form-group">
                            <strong>Option3:</strong>
                            <input type="text" class="form-control" name="option_hindi_3" id="option_hindi_3" value="{{$option_hindi3}}" placeholder="option hindi" />
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="form-group">                        
                            <input type="checkbox" class="form-control" name="checked_hindi_3" id="checked_hindi_3"  />
                        </div>
                    </div>

                    <div class="col-xs-9 col-sm-9 col-md-9">
                        <div class="form-group">
                            <strong>Option4:</strong>
                            <input type="text" class="form-control" name="option_hindi_4" id="option_hindi_4" value="{{$option_hindi4}}" placeholder="option hindi" />
                        </div>
                    </div>

                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="form-group">                        
                            <input type="checkbox" class="form-control" name="checked_hindi_4" id="checked_hindi_4"  />
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <button type="button" class="btn btn-danger add_button">Add Option</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>

                
            </div>
            {!! Form::close() !!}
                        


		</div>		
	</div>
	<!-- /#page-wrapper -->
    <script src="{{ asset('js/question.js') }}"></script>  
@endsection
