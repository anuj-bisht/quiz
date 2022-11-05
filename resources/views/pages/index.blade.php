
@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">
      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Page List')}}</h1>
				</div>
        @include('layouts/flash')
				<!-- /.col-lg-12 -->
			</div>             
  <div class="row pull-right">
    <div class="col-md-12 ">
      <button class="btn btn-success" onclick='location.href="{{url("/")}}/admin/pages/create"'>Add Page</button>
    </div>
  </div>
  <div class="x_panel">
      <div class="x_title">
        <h2>&nbsp;</h2>

        <div class="clearfix"></div>
      </div>
      <div class="x_content">     
          {{ csrf_field() }}             
          <table id="pageData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
              <thead>
                  <tr>
                      
                      <th>Title</th>                      
                      <th>Created Date</th>                      
                      <th>Status</th>
                      <th>Action</th>  
                  </tr>
              </thead>
              <tbody>
                            
              </tbody>
              <tfoot>
                    <tr>                              
                      <th>Title</th>                      
                      <th>Created Date</th> 
                      <th>Status</th>
                      <th>Action</th>  
                  </tr>
              </tfoot>
          </table>                              
        </div>
</div>
      
{!! Form::open(['method' => 'DELETE','route' => ['pages.destroy', 1],'id'=>'deleteRow','style'=>'display:inline']) !!}
      
{!! Form::close() !!}
<script>
        
        var table = '';

        jQuery(document).ready(function() {
          
					//var permissonObj = '<%-JSON.stringify(permission)%>';
					//permissonObj = JSON.parse(permissonObj);


          table = jQuery('#pageData').DataTable({
            'processing': true,
            'serverSide': true,                        
            'lengthMenu': [
              [10, 25, 50, -1], [10, 25, 50, "All"]
            ],
            dom: 'Bfrtip',
            buttons: [                        
            {
            extend:'csvHtml5',
            title: 'Page List',
              exportOptions: {
                columns: [0,1, 2]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {
            extend: 'pdfHtml5',
            title: 'Page List',
              exportOptions: {
                columns: [0,1, 2] //"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
              customize : function(doc){
                    var colCount = new Array();
                    var length = $('#reports_show tbody tr:first-child td').length;
                    //console.log('length / number of td in report one record = '+length);
                    $('#reports_show').find('tbody tr:first-child td').each(function(){
                        if($(this).attr('colspan')){
                            for(var i=1;i<=$(this).attr('colspan');$i++){
                                colCount.push('*');
                            }
                        }else{ colCount.push(parseFloat(100 / length)+'%'); }
                    });
              }
            },
            {
            extend:'pageLength',
            className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            
            }
            ],
            'sPaginationType': "simple_numbers",
            'searching': true,
            "bSort": false,
            "fnDrawCallback": function (oSettings) {
              jQuery('.popoverData').popover();
              // if(jQuery("#userTabButton").parent('li').hasClass('active')){
              //   jQuery("#userTabButton").trigger("click");
              // }
              // jQuery("#userListTable_wrapper").removeClass( "form-inline" );
            },
            'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
              //if (aData["status"] == "1") {
                //jQuery('td', nRow).css('background-color', '#6fdc6f');
              //} else if (aData["status"] == "0") {
                //jQuery('td', nRow).css('background-color', '#ff7f7f');
              //}
              //jQuery('.popoverData').popover();
            },
            'ajax': {
              'url': '{{ url("/") }}/admin/pages/ajaxData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                //d.userFilter = jQuery('#userFilter option:selected').text();
                //d.search = jQuery("#userListTable_filter input").val();
              },
            },          

            'columns': [
                
              {
                  'data': 'title',
                  'className': 'col-md-6',
                  'render': function(data,type,row){
                    var title = (row.title!=null && row.title.length > 30) ? row.title.substring(0,30)+'...' : row.title;
                    return '<a class="popoverData" data-content="'+row.title+'" rel="popover" data-placement="bottom" data-original-title="Name" data-trigger="hover">'+title+'</a>';
                  }
              },
              {
                  'data': 'Created Date',
                  'className': 'col-md-2',
                  'render': function(data,type,row,meta){
                    return row.created_at;
                    
                  }
              },
              {
                'data': 'Status',
                'className': 'col-md-2',
                'render': function(data,type,row){
                    var html = '';
                    if(row.status=='1'){
                      html = '<i class="fa fa-toggle-on" style="color:green; font-size:18px;" ></i>';
                    }else{
                      html = '<i class="fa fa-toggle-off" style="color:red;font-size:18px;"></i>';
                    }                    
                    return html;
                  }  
              },            
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-3',
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-primary" href="'+site_url+'/admin/pages/'+row.id+'/edit">Edit</a>&nbsp;&nbsp;<a class="btn btn-danger" href="javascript:void(0);" onclick="deleteData('+row.id+')">Delete</a>';
                  return buttonHtml;
                }
              }
            ]
          });   
              
          
        });



    function deleteData(id){

      $.confirm({
          title: 'Confirm!',
          content: 'Are you sure want to delete?',
          buttons: {
              confirm: function () {
                $('#deleteRow').attr('action', "{{url('/')}}/admin/pages/"+id).submit();  
              },
              cancel: function () {
                  return true;
              }
          }
      });

    }     
              
      </script>
      <style>
        .dataTables_paginate a {
          background-color:#fff !important;
        }
        .dataTables_paginate .pagination>.active>a{
          color: #fff !important;
          background-color: #337ab7 !important;
        }
        .form-group input,.form-group textarea,.form-group button {
            margin: 10px 0px 10px 0px;
        }
      </style>

@endsection
