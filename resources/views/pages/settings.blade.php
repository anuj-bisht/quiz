@extends('layout.Admin.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')

<div class="right_col" role="main">
@include('layout/Admin/flash')
  <div class="col-md-12 col-xs-12">

                <div class="x_panel">
                  <div class="x_title">
                    <h2>Settings: </h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br>
                    <form class="form-horizontal form-label-left" action="{{ url('/') }}/admin/pages/settings" method="POST">
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
                        <label for="content" class="col-sm-12 control-label">Home About Us</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="6" name="home_about">{{$settings->home_about}}</textarea>
                        </div>
                      </div>


                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Corporate Email</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input class="form-control" placeholder="Corporate Email" type="text" name="corporate_email" value="{{$settings->corporate_email}}">
                        </div>
                      </div>

                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Corporate Phone</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input class="form-control" placeholder="Corporate Phone" type="text" name="corporate_phone" value="{{$settings->corporate_phone}}">
                        </div>
                      </div>

                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Corporate Address</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="3" name="corporate_address">{{$settings->corporate_address}}</textarea>
                        </div>
                      </div>


                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Unit Email</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input class="form-control" placeholder="Unit Email" type="text" name="unit_email" value="{{$settings->unit_email}}">
                        </div>
                      </div>

                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Unit Phone</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          <input class="form-control" placeholder="Unit Phone" type="text" name="unit_phone" value="{{$settings->unit_phone}}">
                        </div>
                      </div>

                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Unit Address</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <textarea class="form-control" rows="3" name="unit_address">{{$settings->unit_address}}</textarea>
                        </div>
                      </div>
                      
                      <div class="form-group" style="float:left">
                        <label for="content" class="col-sm-12 control-label">Copyright</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" placeholder="Copyright" type="text" name="copyright" value="{{$settings->copyright}}">
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
                        <label for="content" class="col-sm-12 control-label">Linkedin Link</label>
                      </div>
                      <div class="form-group">                        
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <input class="form-control" placeholder="LinkedIn" type="text" name="linkedin_link" value="{{$settings->linkedin_link}}">
                        </div>
                      </div>

                     
                      <div class="ln_solid"></div>
                      <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12 ">
                          <button type="button" class="btn btn-primary" onclick="location.href='{{ url('/') }}/users/settings/1'">Cancel</button>
                          <button type="reset" class="btn btn-primary">Reset</button>
                          <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                      </div>

                    </form>
                    
                  </div>
                </div>
</div>
<style>



</style>

@endsection
