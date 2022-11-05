<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="navbar-header">
	<a class="navbar-brand" href="{{url('/')}}/home" style="margin-top:-28px"><img src="{{url('/')}}/img/logo1.png"></a>
		<a class="navbar-brand" href="{{url('/')}}">{{ config('app.APP_NAME')}}</a>
	</div>

	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>

	<ul class="nav navbar-right navbar-top-links">
		
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color:#fff">
				<i class="fa fa-user fa-fw"></i> {{ Auth::user()->name }} <b class="caret"></b>
			</a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="#"><i class="fa fa-user fa-fw"></i> change password</a>
				</li>				
				<li class="divider"></li>
				<li>
				<a class="dropdown-item" href="{{ route('logout') }}"
					onclick="event.preventDefault();
									document.getElementById('logout-form').submit();">
					{{ __('Logout') }}
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>				
				</li>
			</ul>
		</li>
	</ul>
	<!-- /.navbar-top-links -->

	<div class="navbar-default sidebar" role="navigation" style="">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
				
				<li>
					<a href="{{ route('home') }}" class=""><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
				</li>				
				@can('user-list')	
				<li>
					<a href="#"><i class="fa fa-users" aria-hidden="true"></i> User Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
						@can('user-list')		
						<li>
							<a href="{{ route('users.index') }}">Manage Users</a>
						</li>				
						@endcan 
						@can('role-list')
						<li>
							<a href="{{ route('roles.index') }}"> Manage Role</a>
						</li>						
						@endcan 
						<li>
							<a href="{{ route('sendNotifications') }}">Send Notification</a>
						</li>
						
					</ul>
					<!-- /.nav-second-level -->
				</li>	
				@endcan 

				<li>
					<a href="#"><i class="fa fa-map-marker" aria-hidden="true"></i> Education Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
								
						<li>
							<a href="{{ route('districts.index') }}">District List</a>
						</li>	
						<!-- <li>
							<a href="{{ route('blocks.index') }}">Block List</a>
						</li>	 -->
						<li>
							<a href="{{ route('schools.index') }}">School List</a>
						</li>			
												
					</ul>
					<!-- /.nav-second-level -->
				</li>
			

				<li>
					<a href="#"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Pattern Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
								
						<li>
							<a href="{{ route('classes.index') }}">Classes List</a>
						</li>	
						<li>
							<a href="{{ route('subjects.index') }}">Subject List</a>
						</li>	
						<li>
							<a href="{{ route('chapters.index') }}">Chapter List</a>
						</li>	
						<li>
							<a href="{{ route('exams.index') }}">Exam List</a>
						</li>				
												
					</ul>
					<!-- /.nav-second-level -->
				</li>

				
				<li>
					<a href="#"><i class="fa fa-question-circle" aria-hidden="true"></i> Questions Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
								
						<li>
							<a href="{{ route('questions.index') }}">Question List</a>
						</li>				
												
					</ul>
					<!-- /.nav-second-level -->
				</li>
				

				<li>
					<a href="#"><i class="fa fa-credit-card" aria-hidden="true"></i> Membership Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
										
						<li>
							<a href="{{ route('plans.index') }}">Plan List</a>
						</li>		
						
						<li>
							<a href="{{ route('subscriptions.index') }}">Subscription List</a>
						</li>																				
					</ul>
					<!-- /.nav-second-level -->
				</li>	

				
							
				<li>
					<a href="#"><i class="fa fa-file-text" aria-hidden="true"></i> Page Management<span class="fa arrow"></span></a>
					<ul class="nav nav-second-level">
								
						<li>
							<a href="{{ route('pages.index') }}">Page List</a>
						</li>				
												
					</ul>
					<!-- /.nav-second-level -->
				</li>
				

				
				
				<li>
					<a href="{{ url('/') }}/admin/contactus"><i class="fa fa-phone" aria-hidden="true"></i> Contact Enquiry</a>					
				</li>															
				

				<li>
					<a href="{{ url('/') }}/admin/users/settings/1"><i class="fa fa-cogs" aria-hidden="true"></i> Settings</a>					
				</li>															
			</ul>
		</div>
	</div>
</nav>

<script>

</script>