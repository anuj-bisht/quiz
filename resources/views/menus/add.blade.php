@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">
      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Menu List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  
      @include('layouts.flash')    

      
      <div class="row pull-right">
        
      </div>
      <form class="form-horizontal form-label-left" action="{{ url('/') }}/admin/menus/add" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
          
          <div class="form-group" style="float:left">
            <label for="content" class="col-sm-12 control-label">Name</label>
          </div>
          <div class="form-group">                        
            <div class="col-md-12 col-sm-12 col-xs-12">
              <input class="form-control" placeholder="Name" id="menuTitleSlug" type="text" name="name">
            </div>
          </div>

          <div class="form-group" style="float:left">
            <label for="content" class="col-sm-12 control-label">Slug</label>
          </div>
          <div class="form-group">                        
            <div class="col-md-12 col-sm-12 col-xs-12">
              <input class="form-control" placeholder="Slug" type="text" id="realSlug" readonly name="slug">
            </div>
          </div>
          
          <div class="form-group" style="float:left">
            <label for="content" class="col-sm-12 control-label">Image Icon</label>
          </div>
          <div class="form-group">                        
            <div class="col-md-12 col-sm-12 col-xs-12">
              <input type="file" class="form-control" name="file">
            </div>
          </div>  
                    
          <div class="ln_solid"></div>
          <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12 ">
              <button type="button" class="btn btn-primary" onclick="location.href='{{ url('/') }}/admin/menus'">Cancel</button>
              <button type="reset" class="btn btn-primary">Reset</button>
              <button type="submit" class="btn btn-success">Submit</button>
            </div>
          </div>

        </form>  

  </div>		
</div>


@endsection
