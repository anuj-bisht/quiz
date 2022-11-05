@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Trainer Review List')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>&nbsp;</h2>
            </div>
            
        </div>
      </div>

      @include('layouts.flash')
            
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>                                                                            
                  <th>Trainer Name</th>                                          
                  <th>Rating</th>     
                  <th>Reviewed By</th> 
                  <th>Type</th>
                  <th>Comment</th> 
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                                                                            
                  <th>Trainer Name</th>                                          
                  <th>Rating</th>     
                  <th>Reviewed By</th> 
                  <th>Type</th>
                  <th>Comment</th> 
              </tr>
          </tfoot>
      </table>  


		</div>		
	</div>
	<!-- /#page-wrapper -->
  

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
            {extend:'csvHtml5',
              exportOptions: {
                columns: [0, 1]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {extend: 'pdfHtml5',
              exportOptions: {
                columns: [0, 1] //"thead th:not(.noExport)"
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
              'url': '{{ url("/") }}/admin/reviews/ajaxData',
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
                  'data': 'name',
                  'className': 'col-md-3',
                  'render': function(data,type,row){
                    
                    return row.trainer_name;
                  }
              },
              {
                  'data': 'rating',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return row.rating;
                  }
              },
              {
                  'data': 'review_by',
                  'className': 'col-md-3',
                  'render': function(data,type,row){
                    
                    return row.name;
                  }
              },
              {
                  'data': 'type',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.type;
                  }
              },            
              {
                'data': 'comment',                
                'className': 'col-md-3',
                'render': function(data, type, row) {
                  return row.comment;
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
            $('#deleteRow').attr('action', "{{ url('/') }}/admin/gdiets/").submit();  
          },
          cancel: function () {
              return true;
          }
      }
  });

}        



 
</script>


@endsection
