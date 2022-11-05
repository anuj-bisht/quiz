@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Send Notification')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      <div class="row">
        <div class="col-lg-12">
            <div class="pull-left">
                <h2>&nbsp;</h2>
            </div>   
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('sendNotificationAllUser') }}"> Send Notification to All</a>
            </div>         
        </div>
      </div>

      @include('layouts.flash')
      
	<div class="row">
	<div class="col-sm-7">
  	<form method="post" action="{{ route('sendNotificationUser') }}">
	   {{ csrf_field() }}
    	   <div class="form-group">
      	   	<label for="sel1">User Notification(select one):</label>
           	<select multiple class="form-control" id="sel2" name="name[]" required="required">
		   @php
	           foreach($userData as $data){
		     @endphp
		     <option value="{{$data->id}}">{{$data->name}}</option>
	           @php
		   }
      		   @endphp
           	</select>
		<br>
		<label for="sel1">Title</label>
		<input type="text" name="title" class="form-control" required="required">
		<br>
		<div class="md-form">
		<label for="form7">Message</label>
  		<textarea id="form7" name="message" class="md-textarea form-control" rows="3"></textarea>
		</div>
           </div>
           <button type="submit" class="btn btn-primary">Send Notification</button>
  	</form>
	</div>
	</div>
      
		</div>		
	</div>

@endsection
