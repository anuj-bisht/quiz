@extends('layouts.app')
<script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ config('app.APP_NAME')}} - {{ __('Login') }}</h3>
				</div>
				<div class="panel-body">
					<form method="POST" role="form" action="{{ route('login') }}">
						@csrf
						<fieldset>
							<div class="form-group">
								@error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
								<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Enter email address" value="{{ old('email') }}" required autocomplete="email" autofocus>
								
							</div>
							<div class="form-group">
								@error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
								<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" name="password" required autocomplete="current-password" value="">
								
							</div>
							<div class="checkbox">
								
								<label class="form-check-label" for="remember">
									<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
								</label>
								
								@if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
							</div>
							<!-- Change this to a button or input when using this as a form -->
							<button type="submit" class="btn btn-success">
                                    {{ __('Login') }}
							</button>							
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
<script>
$(document).ready(function () {    
    $('body').addClass('bdbg');    
});
</script>
<style>
.bdbg {
  background: url('./img/bg-login.jpg');
  position: relative;
  opacity: 0.75;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  height: 100%;
}
</style>