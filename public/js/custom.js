var site_url = '';
var token = '';
function setSiteURL(url,toekn){
    site_url = url;
    token = token;
}

$(document).ready(function(){
    $("#district_id").on('change',function(e){
    
        e.preventDefault();
        
        var district_id = $(this).val();
        if(!district_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select district!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: site_url+"/admin/blocks/ajaxGetBlockByDistrict",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": district_id            
            },
            success: function(result){
                var html = '<option value="0">Select district</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.block_name+"</option>";
                    });
                }
                $('#block_id').empty().append(html);
                console.log(result);
            }});
    });

   

    $("#html_country_id").on('change',function(e){
                
        e.preventDefault();                 
        var data_country_id = $(this).val();
        
        if(!data_country_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select Country!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: site_url+"/getStatesByCountry",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": data_country_id            
            },
            success: function(result){
                var html = '<option value="0">Select State</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.name+"</option>";
                    });
                }
                $('#html_state_id').empty().append(html);
                console.log(result);
            }});
    });


    $("#class_id").on('change',function(e){
    
        e.preventDefault();
        
        var class_id = $(this).val();
        if(!class_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select class!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: site_url+"/admin/classes/ajaxGetSubjectByClass",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "class_id": class_id            
            },
            success: function(result){
                var html = '<option value="0">Select subject</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.subject_name+"</option>";
                    });
                }
                $('#subject_id').empty().append(html);
                console.log(result);
            }});
    });

    $("#subject_id").on('change',function(e){
    
        e.preventDefault();
        
        var subject_id = $(this).val();
        if(!subject_id){
            jQuery.alert({
                title: 'Alert!',
                content: 'Please select subject!',
            });
            return false;
        }
        
        jQuery.ajax({
            url: site_url+"/admin/chapters/ajaxGetChapterBySubject",
            method: 'post',
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "subject_id": subject_id            
            },
            success: function(result){
                var html = '<option value="0">Select chapter</option>'
                if(result.status){
                    jQuery.each(result.data, function(index, item) {
                        html += "<option value='"+item.id+"'>"+item.chapter_name+"</option>";
                    });
                }
                $('#chapter_id').empty().append(html);
                console.log(result);
            }});
    });
    

    $('#multiselect').multiselect();



});


$('#uploadCSV').submit(function(e){
    e.preventDefault();
    var imgname  =  $('input[type=file]').val();    
    //var size  =  $('#file')[0].files[0].size;
    var size  =  $('#file')[0].size;        
    var ext =  imgname.substr( (imgname.lastIndexOf('.') +1) );
    if(ext=='csv' || ext=='CSV'){
        if(size<=20000000){

            var frm = $('#uploadCSV');
            var formData = new FormData(frm[0]);
            $('#uploadcsvbtn').attr('disabled',true);
            $('#spinimg').css('display','block');
            
            $.ajax({
                type: "POST",
                url: site_url+"/admin/uploads/csvupload",    
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: formData,           
                success: function(res) {
                    if(res.status == '2' )
                    {
                        $('#exportExData')[0].click();
                    }
                    if(res.status){
                        jQuery.alert({
                            title: 'Alert!',
                            content: res.message,
                        });
                    }else{
                        jQuery.alert({
                            title: 'Alert!',
                            content: res.message,
                        });
                    }                      
                    $('#uploadcsvbtn').attr('disabled',false);  
                    $('#spinimg').css('display','none');  
                       
                },
                error:function(request, status, error) {
                    console.log("ajax call went wrong:" + request.responseText);
                }
            });
        }else{
            jQuery.alert({
                title: 'Alert!',
                content: 'Sorry File size exceeding from 20 Mb',
            });                
        }
    }else{
        jQuery.alert({
            title: 'Alert!',
            content: 'Sorry Only you can uplaod CSV file type',
        });                
    }                        
    
});
 


function startTimer(duration, display) {
    var timer = duration, hours, minutes, seconds;
    setInterval(function () {
        hours = parseInt(timer / 60, 10);
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        //display.textContent = minutes + ":" + seconds;
        $('#'+display).html(hours + ":" +minutes + ":" + seconds);

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}

//hh:mm:ss
function hmsToSecondsOnly(str) {
    var p = str.split(':'),
        s = 0, m = 1;

    while (p.length > 0) {
        s += m * parseInt(p.pop(), 10);
        m *= 60;
    }

    return s;
}



function notificationData(){

    var str = '<li><div>Loading..........</div></li>';
    $('#notification_ul').html(str); 
	jQuery.ajax({
		url: site_url+"/notifications/ajaxNotificationData",
		method: 'post',
		data: {
			"_token": $('meta[name="csrf-token"]').attr('content')
		},
		success: function(result){
			
			var html = '';			
			if(result.status){
                if(result.data.length>0){
                    $.each(result.data,function(index,value){
                        html += '<li><a href="#">';
                        html += '<div><i class="fa fa-comment fa-fw"></i>'+value.notification_message+'<span class="pull-right text-muted small">'+value.created+' ago</span></div>';
                        html += '</a></li>';				
                    })
                }else{
                    html += '<li><a href="#">';
                    html += '<div>No Notification<span class="pull-right text-muted small"></span></div>';
                    html += '</a></li>';				
                }
                
				$('#notification_ul').html(html);    
			}
			
			//console.log(result);
	}});
}