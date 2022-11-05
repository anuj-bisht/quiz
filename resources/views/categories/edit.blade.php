@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Edit Category')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')

            
            {!! Form::model($category, ['action' => ['CategoryController@update', $cateogry->id], 'method' => 'PUT','enctype'=>'multipart/form-data']) !!}

            @csrf
            <div class="row">

                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', $category->name, array('placeholder' => 'Category Name','class' => 'form-control')) !!}
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Image Icon:</strong>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>

                @if(file_exists($category->file_path))
                <div class="form-group">                        
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <img src="{{$category->image}}" style="max-width:200px; max-height:200px">
                </div>
                </div>  
                @endif  
                
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea class="form-control ckeditor" id="body" name="description" required  rows="5" placeholder="Description">{{$category->description}}</textarea>                        
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
