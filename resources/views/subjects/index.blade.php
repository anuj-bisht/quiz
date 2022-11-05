@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Subject List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  


      <div class="row" style="margin-bottom:15px;">                    
          @include('layouts.upload')
          <div class="col-md-7 margin-tb">            
              <div class="pull-right">
                  <a class="btn btn-success" href="{{ route('subjects.create') }}"> Create {{$ctrl_name}}</a>
              </div>
          </div>
        </div> 

      @include('layouts.flash')
      <button type="button" onclick="changeStatus()">Delete All</button>
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>   
              <th><input type="checkbox" id="selectAll">
                    <label for="selectAll"></label></th>                                                                                 
                  <th>Subject Name</th>                      
                  <th>Class Name</th>  
                  <th>Banner</th>  
                  <th>Logo</th>    
                  <th>Status</th>    
                  <th>Action</th> 
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>   
              <th></th>                                                                                 
                  <th>Subject Name</th>                      
                  <th>Class Name</th>  
                  <th>Banner</th>  
                  <th>Logo</th>    
                  <th>Status</th>    
                  <th>Action</th> 
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
  
  {!! Form::open(['method' => 'DELETE','route' => ['plans.destroy', 1],'id'=>'deleteRow','style'=>'display:inline']) !!}
      
  {!! Form::close() !!}
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
        
    var url = "{{url('/')}}";
    var table = '';

    jQuery(document).ready(function() {
          
					
          table = jQuery('#tableData').DataTable({
            'processing': true,
            'serverSide': true,                        
            'lengthMenu': [
              [10, 25, 50, -1], [10, 25, 50, "All"]
            ],
            dom: 'Bfrtip',
            buttons: [                        
            {
            extend:'csvHtml5',
            title: 'Subject List',
              exportOptions: {
              	stripHtml: false,
                columns: [1, 2, 3, 4, 5]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {
            extend: 'pdfHtml5',
            title: 'Subject List',
              exportOptions: {
              stripHtml: false,
                columns: [1, 2, 3, 4,5] //"thead th:not(.noExport)"
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
            "bSort": true,
            "fnDrawCallback": function (oSettings) {
              
            },
            'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
              //if (aData["status"] == "1") {
                //jQuery('td', nRow).css('background-color', '#6fdc6f');
              //} else if (aData["status"] == "0") {
                //jQuery('td', nRow).css('background-color', '#ff7f7f');
              //}
              //jQuery('.popoverData').popover();
            },
						"initComplete": function(settings, json) {						
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/admin/{{$view_name}}/ajaxData',
              'headers': {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              'type': 'post',
              'data': function(d) {
                //d.statusFilter = jQuery('#statusFilter').val();
                d.parent = jQuery('#parentFilter option:selected').val();
                //d.search = jQuery("#msds-select option:selected").val();
              },
            },          

            'columns': [
              {
                  'data': 'checkbox',
                  'orderable': false,
                  'className': 'col-md-1',
                  'render': function(data, type, row) {
                    console.log(type);
                  var buttonHtml = '<div class="checkbox"><input type="checkbox" class="pl" id="tr-checkbox'+row.row_id+'" value="'+row.id+'"><label for="tr-checkbox3"></label></div>';
                  return buttonHtml;
                }
              },             
              {
                  'data': 'subject_name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.subject_name;
                  }
              },
              {
                  'data': 'class_name',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.class_name;
                  }
              },
              {
                  'data': 'banner',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return '<img src="'+row.subject_banner+'" style="max-width:100px;max-height:100px;">';
                  }
              },
              {
                  'data': 'logo',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return '<img src="'+row.subject_logo+'" style="max-width:100px;max-height:100px;">';
                  }
              },
              {
                  'data': 'status',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    var status = '';
                    if(row.status=='Y'){
                      status = 'Active';
                    }else{
                      status = 'De-active';
                    }
                    return status;
                  }
              },            
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-3',
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-primary" href="'+url+'/admin/{{$view_name}}/'+row.id+'/edit">Edit</a>&nbsp;&nbsp;<a class="btn btn-danger" href="javascript:void(0);" onclick="deleteData('+row.id+')">Delete</a>';
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
            $('#deleteRow').attr('action', url+"/admin/{{$view_name}}/"+id).submit();  
          },
          cancel: function () {
              return true;
          }
      }
  });

} 

$(document).ready(function() {
  var $selectAll = $('#selectAll'); // main checkbox inside table thead
  var $table = $('.table'); // table selector 
  var $tdCheckbox = $table.find('tbody input:checkbox'); // checboxes inside table body
  
  var tdCheckboxChecked = 0; // checked checboxes
  
  // Select or deselect all checkboxes depending on main checkbox change
  $selectAll.on('click', function () {
    var $tdCheckbox = $table.find('tbody input:checkbox');
    // console.log($tdCheckbox);
    $tdCheckbox.prop('checked', this.checked);
  });

  // Toggle main checkbox state to checked when all checkboxes inside tbody tag is checked
  $tdCheckbox.on('change', function(e){
    tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length; // Get count of checkboxes that is checked
    // if all checkboxes are checked, then set property of main checkbox to "true", else set to "false"
    $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
  })
}); 

function changeStatus()
{
  var ids = [];
         $('input:checkbox.pl').each(function () {
           var sThisVal = (this.checked ? $(this).val() : "");
           if(sThisVal != '')
           {
               ids.push(sThisVal);
           }
        });
        if(ids.length > 0)
        {
          Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
                url : "{{ route('destroySubjects')}}",
                type: "POST",
                data : {'ids':ids},
                headers: {

                    'X-CSRF-TOKEN': $('input[name="_token"]').val()

                },
                success:function(response)
                {
                    if(response.success == true)
                    {
                      Swal.fire(
                        'Deleted!',
                        response.message,
                        'success'
                      )
                        location.reload();
                    }
                }
                
            });
            
          }
        })
        }
        else{
          Swal.fire(
            'Warning?',
            'Please Select Atleast One Row',
            'question'
          )
        }
  
  
}



 
</script>


@endsection
