@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">

      <div class="row">
        <div class="row">
          <div class="col-lg-12">
            <h1 class="page-header">{{__('Send Notification')}}</h1>
          </div>
          
          <div class="col-lg-12" id="messageDiv">
              
          </div>
          
          <div class="col-xs-6 col-sm-6 col-md-6">
              <select name="from" id="multiselect2" class="form-control" size="8" multiple="multiple">
                @if(count($optionData)>0)
                  @foreach($optionData as $k=>$v)
                      
                          if(count($optionData[$v]){
                              <optgroup label="{{$k}}">
                              @foreach($optionData[$k] as $key=>$value)
                                  <option value="{{$value->id}}">{{$value->name}}</option>
                              @endforeach
                              </optgroup>    
                          }
                      
                  @endforeach
                @endif
              </select>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">                      
                &nbsp;
                </div>
           </div>                
          <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Notification:</strong>
                {!! Form::select('notification_id', $subjects, null, array('placeholder'=>'Choose Notification','id'=>'notificationSubjectList','class' => 'form-control','onchange'=>'notificationSubjectChange()')) !!}
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                  <strong>Subject:</strong>
                  {!! Form::text('subject', null, array('id'=>'notification_subject','placeholder' => 'Subject','class' => 'form-control')) !!}
              </div>
          </div>                
          <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                  <strong>Message:</strong>
                  {!! Form::textarea('message', null, array('id'=>'notification_message','placeholder' => 'Message','rows'=>5,'class' => 'form-control')) !!}
              </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
              <div class="form-group">
                  &nbsp;
              </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12">
              <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
              <button type="submit" id="notificationSubmitButton" class="btn btn-primary" onclick="sendNotification()">Send</button>
          </div>
          <!-- /.col-lg-12 -->
        </div> 
      </div>
      <script>
      function sendNotification(){
        var notification_subject = $('#notification_subject').val();
        var notification_message = $('#notification_message').val();
        var selectedValues = $('#multiselect2').val();
        

        if(notification_subject==""){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please enter subject!',
            });
            return false;
        }

        if(notification_message==""){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please enter message!',
            });
            return false;
        }

        if(selectedValues==""){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select users!',
            });
            return false;
        }

        
        jQuery('#notificationSubmitButton').attr('disabled',true);

        jQuery.ajax({
            url: site_url+"/notifications/ajaxSendNotification",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "subject": notification_subject,"message": notification_message,"userlist": selectedValues           
            },
            success: function(result){
                if(result.status){
                  $('#messageDiv').html("<span style='color:green'>"+result.message+"</span>");
                }else{
                  $('#messageDiv').html("<span style='color:red'>"+result.message+"</span>");
                }
                //$('#messageDiv').html("<span></span>");
                console.log(result);

                jQuery('#notificationSubmitButton').attr('disabled',false);
            }
            
        });

      }

      function notificationSubjectChange(){

        var notificationSubjectList = $('#notificationSubjectList').val();            

        if(notificationSubjectList==""){
            return false;
        }

                
       
        jQuery.ajax({
            url: site_url+"/notifications/ajaxGetNotificationById",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": notificationSubjectList           
            },
            success: function(result){
                if(result.status){
                  $('#notification_subject').val(result.data.subject);
                  $('#notification_message').val(result.data.message);
                }
               
            }
            
        });

      }


      
      </script>
      

  
	<!-- /#page-wrapper -->
            
@endsection
