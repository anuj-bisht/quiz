@extends('layouts.app')

@section('content')

	<!-- Navigation -->
	@include('layouts.left')

	<div id="page-wrapper">
		<div class="container-fluid">


            <div class="row">
                    <div class="col-lg-10">
                        <h1 class="page-header">{{__('Show Role')}}</h1>                        
                    </div>		                    
            </div>
            
            @include('layouts.flash')
            
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $role->name }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Permissions:</strong>
                        @if(!empty($rolePermissions))
                            @foreach($rolePermissions as $v)
                                <label class="label label-success">{{ $v->name }},</label>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a class="btn btn-primary" href="{{ route('roles.index') }}"> Back</a>
                </div>                
            </div>                                  


		</div>		
	</div>
	<!-- /#page-wrapper -->
            
@endsection