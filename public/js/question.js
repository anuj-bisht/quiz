$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var x = 1; //Initial field counter is 1
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var wrapper1 = $('.field_wrapper_1'); //Input field wrapper
                     
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        alert($('.field_wrapper').children().length);
        var fieldHTML = '';
        if(x < maxField){ 
            x++; //Increment field counter
            fieldHTML +=  '<div><div class="col-xs-9 col-sm-9 col-md-9">';
            fieldHTML +=   '<div class="form-group">';
            fieldHTML +=      '<strong>Option '+(x+1)+':</strong>';
            fieldHTML +=      '<input placeholder="Option'+(x+1)+'" class="form-control" name="option['+(x+1)+']" type="text">';
            fieldHTML +=   '</div>';
            fieldHTML +=  '</div>';
            fieldHTML +=  '<div class="col-xs-2 col-sm-2 col-md-2">';
            fieldHTML +=   '<div class="form-group">';
            fieldHTML +=     '<input class="form-control" id="answer" style="margin-top: 20px;" name="answer['+(x+1)+']" type="checkbox">';
            fieldHTML +=   '</div>';
            fieldHTML +=  '</div>';
            fieldHTML +=  '<div class="col-xs-1 col-sm-1 col-md-1 remove_button" style="margin-top:23px;">';
            fieldHTML +=   '<div class="form-group">';
            fieldHTML +=     '<span class="label label-danger">X</span>';
            fieldHTML +=   '</div>';
            fieldHTML +=  '</div></div>';
            
            $(wrapper).append(fieldHTML); //Add field html
            $(wrapper1).append(fieldHTML);
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        //$(this).remove(); //Remove field html
        x--; //Decrement field counter
    });
    $(wrapper1).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        //$(this).remove(); //Remove field html
        x--; //Decrement field counter
    });
});

$('#question_type').on('change',function(){
    var option_val = $(this).val();        
    if(!option_val){
        $.alert({
            title: 'Alert!',
            content: 'Please select question type!',
        });
    }

    if(option_val=='Y'){
        $('#image_question').css('display','block');            
    }else{
        $('#image_question').css('display','none');            
    }
})

imgInp.onchange = evt => {
    const [file] = imgInp.files
    if (file) {
        blah.src = URL.createObjectURL(file)
    }
}


$('#questionForm').submit(function(e){
    e.preventDefault();
    
    var qtype = $('#question_type').val();

    if(qtype=='Y'){
        var imgname  =  $('input[type=file]').val();            
        if(imgname==""){
            jQuery.alert({
                    title: 'Alert!',
                    content: 'Please upload image',
            });
            return false;
        }
        //console.log("=====",$('#imgInp')[0].size);
        //var size  =  $('#file')[0].files[0].size;
        var size  =  $('#imgInp')[0].size;        
        var ext =  imgname.substr( (imgname.lastIndexOf('.') +1) ).toLowerCase();
        if(!(ext=='png' || ext=='jpg' || ext=='jpeg' || ext=='gif')){
            if(size>=20000000){
                jQuery.alert({
                    title: 'Alert!',
                    content: 'Sorry File size exceeding from 20 Mb',
                });
            }

            jQuery.alert({
                    title: 'Alert!',
                    content: 'Please upload only Jpg,Png and gif file',
            });

            return;
        }
    }
    
    
    var frm = $('#questionForm');
    var url = frm.attr('action');
    var formData = new FormData(frm[0]);
    //$('#uploadcsvbtn').attr('disabled',true);
    //$('#spinimg').css('display','block');
    
    $.ajax({
        type: "POST",
        url: url,    
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        data: formData,           
        success: function(res) {
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
            //$('#uploadcsvbtn').attr('disabled',false);  
            //$('#spinimg').css('display','none');     
        },
        error:function(request, status, error) {
            console.log("ajax call went wrong:" + request.responseText);
        }
    });
    
});