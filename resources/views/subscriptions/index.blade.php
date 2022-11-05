@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Subscription List')}}</h1>
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
                  <th>&nbsp;</th>                                                                                                                                                        
                  <th>Plan</th>                                        
                  <th>User</th>  
                  <th>Start</th>  
                  <th>Next Billing</th>    
                  <th>Created</th>                                        
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>
                  <th>&nbsp;</th>                                                                            
                  <th>Plan</th>                                        
                  <th>User</th>  
                  <th>Start</th>  
                  <th>Next Billing</th>    
                  <th>Created</th>                      
                  
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
            {
            extend:'csvHtml5',
            title: 'Subscription List',
              exportOptions: {
                columns: [1, 2, 3, 4, 5]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {
            extend: 'pdfHtml5',
            title: 'Subscription List',
              exportOptions: {
                columns: [1, 2, 3, 4, 5] //"thead th:not(.noExport)"
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
              'url': '{{ url("/") }}/admin/subscriptions/ajaxData',
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
              {   'data': 'Plan',               
                  'className': 'details-control col-md-2',                  
                  'render': function(data,type,row){                    
                    return '';
                  }
              },                
              {
                  'data': 'Plan',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.plan_name;
                  }
              },
              {
                  'data': 'user',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.username;
                  }
              },
              {
                  'data': 'start',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.start_date;
                  }
              },
              {
                  'data': 'next_bill_date',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.end_date;
                  }
              },
              {
                  'data': 'created',
                  'className': 'col-md-2',
                  'render': function(data,type,row){
                    
                    return row.created_at;
                  }
              }
            ]
          });


         $('#tableData tbody').on('click', 'td.details-control', function () {
              var tr = $(this).closest('tr');
              var row = table.row( tr );
      
              if ( row.child.isShown() ) {
                  // This row is already open - close it
                  row.child.hide();
                  tr.removeClass('shown');
              }
              else {
                  // Open this row
                  row.child( format(row.data()) ).show();
                  tr.addClass('shown');
              }
          });         

  });

  function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>Full name:</td>'+
            '<td>'+d.username+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extension number:</td>'+
            '<td>sdfasdf</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
        '</tr>'+
    '</table>';
}
</script>

<style>
td.details-control {
    background: url('../images/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('../images/details_close.png') no-repeat center center;
}
</style>

@endsection
