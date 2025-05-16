<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="name">Name</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="mobile_no" value="{{ old('name') }}" required autofocus>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <label for="phoneno">Phone Number</label>
        <input id="phoneno" type="text" class="form-control @error('phoneno') is-invalid @enderror" name="trade" value="{{ old('phoneno') }}" required>
        @error('phoneno')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">
            {{ __('Login') }}
        </button>
    </div>
</form>
