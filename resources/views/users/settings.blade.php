@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">
		@include('layouts.flash')

      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Settings')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  
			
			<form class="form-horizontal form-label-left" action="{{ url('/') }}/admin/users/settings/1" method="POST">
				  {{ csrf_field() }}
				  
				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Admin Email</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <input class="form-control" placeholder="Email" type="text" name="admin_email" value="{{$settings->admin_email}}">
					</div>
				  </div>

				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Terms and Condition</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <textarea class="form-control ckeditor" rows="8" name="terms_and_condition">{{$settings->terms_and_condition}}</textarea>
					</div>
				  </div>

					<div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">FAQ's</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <textarea class="form-control ckeditor" rows="8" name="faq">{{$settings->faq}}</textarea>
					</div>
				  </div>


				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Privacy Policy</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <textarea class="form-control ckeditor" rows="8" name="privacy_policy">{{$settings->privacy_policy}}</textarea>
					</div>
				  </div>

				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Facebook Link</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <input class="form-control" placeholder="Facebook link" type="text" name="fb_link" value="{{$settings->fb_link}}">
					</div>
				  </div>

				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Twitter Link</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					  <input class="form-control" placeholder="Twitter" type="text" name="twitter_link" value="{{$settings->twitter_link}}">
					</div>
				  </div>

				  <div class="form-group" style="float:left">
					<label for="content" class="col-sm-12 control-label">Instagram Link</label>
				  </div>
				  <div class="form-group">                        
					<div class="col-md-12 col-sm-12 col-xs-12">
					<input class="form-control" placeholder="Instagram" type="text" name="insta_link" value="{{$settings->insta_link}}">
					</div>
				  </div>
				  
				  				 
				  <div class="ln_solid"></div>
				  <div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12 ">
					  <button type="button" class="btn btn-primary" onclick="location.href='{{ url('/') }}/admin/users/settings/1'">Cancel</button>
					  <button type="reset" class="btn btn-primary">Reset</button>
					  <button type="submit" class="btn btn-success">Submit</button>
					</div>
				  </div>

			</form>
      

		</div>		
	</div>

  
	
 
@endsection

