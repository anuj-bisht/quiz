@extends('layouts.app')

@section('content')


	<div id="page-wrapper">		

			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Error')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

			<div class="row">
				<div class="col-md-12">
					We are facing some issue on web and will correct it soon
						<br>
					 {{$data}}
				</div>
        
			</div>             	
	</div>
	<!-- /#page-wrapper -->
  

  <script>

  $(document).ready(function(){
      $('#messageSentForm').submit(function(e){
        e.preventDefault();
        var user_id = $('#inputUserId').val();
        
        $.ajax({
            type: "POST",
            url: "{{url('/')}}/admin/chats/addChat",    
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: $('#messageSentForm').serialize(),           
            success: function(res) {
                if(res.status){
                  $('#msgtxtbox').val('');
                  $('#chatspan_'+user_id).html('');
                  getChatById(user_id);
                }else{
                  jQuery.alert({
                      title: 'Alert!',
                      content: res.message,
                  });
                }          
                
                //$('#vehicleModal').modal('show'); 
                          
            },
            error:function(request, status, error) {
                console.log("ajax call went wrong:" + request.responseText);
            }
        });
        
      });

      setInterval(function(){ 
        
        $.ajax({
            type: "POST",
            url: "{{url('/')}}/admin/chats/getUnReadChat",    
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {},           
            success: function(res) {
              var str = '';
                if(res.status==1){
                  $.each(res.data, function( index, value) {
                    $('#chatspan_'+value.id).html('('+value.read_count+')');                    
                  });                  
                }            
            },
            error:function(request, status, error) {
                console.log("ajax call went wrong:" + request.responseText);
            }
        });
      }, 5000);

  })
  function getChatById(id){
    $('#inputUserId').val(id);
    $('#messageSentForm').css('display','block');
    $.ajax({
        type: "POST",
        url: "{{url('/')}}/admin/chats/getChatById",    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {"id":id},           
        success: function(res) {
          var str = '';
            if(res.status==1){
              $('#chatspan_'+id).html(''); 
              $.each(res.data, function( index, value) {
                if(value.from_id==id){
                  str += '<tr><td style="float:left;"><p class="chat-s">'+value.message+'</p></td></tr>';  
                }else{
                  str += '<tr><td style="float:right;"><p class="chat-r">'+value.message+'</p></td></tr>';
                }
                
              });
              $('#chatdatalist').html(str);
            }            
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
  }
  </script>
<style>
.chat-s{
  padding:10px;
  background:#E6E4E4;
  border-radius:5px 0px 5px 5px;
}

.chat-r{
  padding:10px;
  background:#E9D2D2;
  border-radius:5px 0px 5px 5px;
}

.blink_me {
  animation: blinker 1s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
</style>
@endsection
