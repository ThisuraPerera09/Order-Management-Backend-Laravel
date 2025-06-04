@extends('layouts.app')

@section('content')
    <h2>Login</h2>
    <form id="loginForm">
        @csrf

        <label for="email">Email:</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <span class="text-red-500">{{ $message }}</span>
        @enderror

        <label for="password">Password:</label>
        <input id="password" type="password" name="password" required>
        @error('password')
            <span class="text-red-500">{{ $message }}</span>
        @enderror

        <button type="submit">Login</button>
    </form>

    <div id="error-message" style="color: red;"></div>

    @if (session('status'))
        <div>{{ session('status') }}</div>
    @endif

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
       
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $('#loginForm').submit(function(event) {
                event.preventDefault(); 

                var formData = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    _token: csrfToken 
                };

                $.ajax({
                    url: '/api/login', 
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response); 
                        alert('Login successful!'); 

                        // Store the token in local storage or cookies
                        localStorage.setItem('auth_token', response.token);

                        window.location.replace('/dashboard'); 
                    },
                    error: function(xhr, status, error) {
                        console.error(error); 
                        let response = JSON.parse(xhr.responseText);
                        $('#error-message').text(response.message);
                    }
                });
            });
        });
    </script>
@endsection
