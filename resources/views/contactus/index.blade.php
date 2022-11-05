@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


      <div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">{{__('Contact Enquiry')}}</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>  

      
      @include('layouts.flash')
            
      <table id="tableData" class="table-responsive table table-striped table-bordered" style="font-size:12px;width:100% !important">
          
          <thead>
              <tr>                                                                            
                  <th>Name</th>                      
                  <th>Email</th>      
                  <th>Phone</th>      
                  <th>Message</th>      
                  <th>Action</th> 
              </tr>
          </thead>
          <tbody>
                        
          </tbody>
          <tfoot>
              <tr>                                                                            
                  <th>Name</th>                      
                  <th>Email</th>      
                  <th>Phone</th>      
                  <th>Message</th>      
                  <th>Action</th> 
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
            title: 'Contact Enquiry',
              exportOptions: {
                columns: [0, 1, 2]//"thead th:not(.noExport)"
              },
              className: 'btn btn-default',
                init: function(api, node, config) {
                  $(node).removeClass('dt-button')
                },
            },
            {
            extend: 'pdfHtml5',
            title: 'Contact Enquiry',
              exportOptions: {
                columns: [0, 1, 2] //"thead th:not(.noExport)"
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
              if (aData.replied == "Y") {
                $('td', nRow).css('background-color', '#90ee90');
              } else if (aData.replied == "N") {
                $('td', nRow).css('background-color', '#fff');
              }
            },
						"initComplete": function(settings, json) {						
              //jQuery('.popoverData').popover();
					  },
            'ajax': {
              'url': '{{ url("/") }}/admin/contactus/ajaxData',
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
                    
                    return row.name;
                  }
              },
              {
                  'data': 'email',
                  'className': 'col-md-3',
                  'render': function(data,type,row){
                    
                    return row.email;
                  }
              },
              {
                  'data': 'phone',
                  'className': 'col-md-3',
                  'render': function(data,type,row){
                    
                    return row.phone;
                  }
              },
              {
                  'data': 'message',
                  'className': 'col-md-1',
                  'render': function(data,type,row){
                    
                    return '<a href="#" title="'+row.message+'"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                  }
              },            
              {
                'data': 'Action',
                'orderable': false,
                'className': 'col-md-2',
                'render': function(data, type, row) {
                  var buttonHtml = '<a class="btn btn-primary" href="'+url+'/admin/contactus/'+row.id+'/edit">Reply</a>';
                  return buttonHtml;
                }
              }
            ]
          });     


  

  });

  
</script>


@endsection
