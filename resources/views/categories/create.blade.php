@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Create New Category')}}</h1>                        
                    </div>		                    
            </div>

            @include('layouts.flash')
            
            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">

            @csrf
            <div class="row">
                

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Category Name','class' => 'form-control')) !!}
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Image Icon:</strong>
                        <input type="file" class="form-control" name="file">
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea class="form-control ckeditor" id="body" name="description" required  rows="5" placeholder="Description"></textarea>                        
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
