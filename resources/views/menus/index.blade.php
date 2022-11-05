
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
        @include('layouts/flash')
				<!-- /.col-lg-12 -->
			</div>             
      

  @inject('menu', 'App\Menu')
  <div class="row pull-right">
    <div class="col-md-12 ">
      <button class="btn btn-primary" onclick='location.href="{{url("/")}}/admin/menus/add"'>Add Menu</button>
    </div>
  </div>
  <div class="cf nestable-lists">     
    <div class="dd" id="nestable">
      {!! $menu->build_menu(0,$menuData) !!}
    </div>
  </div>      



<div id="assignPage" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <form method="post" name="pageAssignForm" action="{{url('/')}}/admin/menus/assignpageSubmit" id="pageAssignForm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Assign Page</h4>
        </div>
        <div class="modal-body">      
          <input type="hidden" name="menu_id" id="pageAssignMenuId" value="">
          <div id="messageData"></div>        
        </div>      
        <div class="modal-footer">
          <button type="submit" id="assignPageBtn" class="btn btn-primary">Assign</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

		</div>		
	</div>




<style>
.cf:after { visibility: hidden; display: block; font-size: 0; content: " "; clear: both; height: 0; }
* html .cf { zoom: 1; }
*:first-child+html .cf { zoom: 1; }


.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 600px; list-style: none; font-size: 13px; line-height: 20px; }

.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-collapsed .dd-list { display: none; }

.dd-item,
.dd-empty,
.dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }

.dd-handle { display: block; height: 30px; margin: 5px 0; padding: 5px 10px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc;
    background: #fafafa;
    background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:    -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:         linear-gradient(top, #fafafa 0%, #eee 100%);
    -webkit-border-radius: 3px;
            border-radius: 3px;
    box-sizing: border-box; -moz-box-sizing: border-box;
}
.dd-handle:hover { color: #2ea8e5; background: #fff; }

.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
.dd-item > button[data-action="collapse"]:before { content: '-'; }

.dd-placeholder,
.dd-empty { margin: 5px 0; padding: 0; min-height: 30px; background: #f2fbff; border: 1px dashed #b6bcbf; box-sizing: border-box; -moz-box-sizing: border-box; }
.dd-empty { border: 1px dashed #bbb; min-height: 100px; background-color: #e5e5e5;
    background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                      -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-image:    -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                         -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-image:         linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
                              linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
    background-size: 60px 60px;
    background-position: 0 0, 30px 30px;
}

.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
.dd-dragel .dd-handle {
    -webkit-box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
            box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1);
}

/**
 * Nestable Extras
 */

.nestable-lists { display: block; clear: both; padding: 30px 0; width: 100%; border: 0; }

#nestable-menu { padding: 0; margin: 20px 0; }

#nestable-output,

@media only screen and (min-width: 700px) {

    .dd { float: left; width: 48%; }
    .dd + .dd { margin-left: 2%; }

}

.dd-hover > .dd-handle { background: #2ea8e5 !important; }

/**
 * Nestable Draggable Handles
 */

.dd3-content { display: block; height: 30px; margin: 5px 0; padding: 5px 10px 5px 40px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc;
    background: #fafafa;
    background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:    -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
    background:         linear-gradient(top, #fafafa 0%, #eee 100%);
    -webkit-border-radius: 3px;
            border-radius: 3px;
    box-sizing: border-box; -moz-box-sizing: border-box;
}
.dd3-content:hover { color: #2ea8e5; background: #fff; }

.dd-dragel > .dd3-item > .dd3-content { margin: 0; }

.dd3-item > button { margin-left: 30px; }

.dd3-handle { position: absolute; margin: 0; left: 0; top: 0; cursor: pointer; width: 30px; text-indent: 100%; white-space: nowrap; overflow: hidden;
    border: 1px solid #aaa;
    background: #ddd;
    background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
    background:    -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
    background:         linear-gradient(top, #ddd 0%, #bbb 100%);
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
.dd3-handle:before { content: '≡'; display: block; position: absolute; left: 0; top: 3px; width: 100%; text-align: center; text-indent: 0; color: #fff; font-size: 20px; font-weight: normal; }
.dd3-handle:hover { background: #ddd; }

/**
 * Socialite
 */

.socialite { display: block; float: left; height: 35px; }
</style>          

<script src="{{ url('/') }}/js/jquery.nestable.js"></script>

<script>                     
  function editpage(id){
    window.location.href = "{{url('/')}}/admin/clients/edit/"+id
  }
  

  function deleteRow(id){
    if(confirm('Are you sure want to delete record?')){
      window.location.href = "{{url('/')}}/admin/clients/destroy/"+id;
    }
  }          

  $(document).ready(function(){

    var updateOutput = function(e) {
        var list   = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));

        } else {
            output.val('JSON browser support required for this demo.');
        }
    };

    // activate Nestable for list 1
    $('#nestable').nestable({
        group: 1
    }).on('change', function(){
		//alert('ajax call')
	       updateOutput($('#nestable').data('output', $('#nestable-output')));
    });
    

    $('.deletemenu').on('click',function(){
      var id = $(this).attr('id');
      $.ajax({
        type: "post",
        url: '{{ url("/") }}/admin/menus/destroy',
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        data: {
          "id": id
        },
        success: function(response) {
          var response = JSON.parse(response);  
          if (response.status == 1) {            
            jQuery.alert({
              title: "Success",
              content: 'Menu Deleted',
            });
            location.reload();
          } else {
            jQuery.alert({
              title: "Error!",
              content: response.message,
            });
          }
          return false;
      }
    })
  });
  
});

function loadPage(id){
   $('#assignPage').modal('show');
   $('#pageAssignMenuId').val(id);
   $.ajax({
        type: "post",
        url: '{{ url("/") }}/admin/menus/assignpage',
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        data: {
          "id": id
        },
        success: function(response) {
          var response = JSON.parse(response);  
          var str = '<div class="row">';          
          if (response.status == 1) {   
            $.each(response.data, function(index,value) {
              var selected = "";
              if(value.selected){
                selected = "checked";
              }
              str += '<div class="col-md-1"><input type="checkbox" name="page_id[]" value="'+value.page_id+'" '+selected+'></div>';
              str += '<div class="col-md-11">'+value.page_title+'</div>';            
            });         
            str += '</div>';    
            $('#messageData').html(str);
          } else {
            jQuery.alert({
              title: "Error!",
              content: response.message,
            });
          }
          return false;
      }
    });
}



  $("#pageAssignForm").submit(function(e) {
    e.preventDefault(); 
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), 
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
          var data = JSON.parse(data);
          if(data.status){
            $('#assignPage').modal('hide');
          }else{
            jQuery.alert({
              title: "Error!",
              content: data.message,
            });
          }
        }
    });
  });

</script>
      
@endsection
