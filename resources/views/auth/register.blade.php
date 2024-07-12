@extends('layouts.app')

@section('content')
    <h2>Register</h2>
    <form id="registrationForm" method="POST" action="{{ route('register') }}">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="role_type">Role:</label>
        <select id="role_type" name="role_type" required>
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select>
        <br>
        <button type="submit" id="registerBtn">Register</button>
    </form>

    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registrationForm').submit(function(event) {
                event.preventDefault(); 

                var formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    role_type: $('#role_type').val()
                };

                $.ajax({
                    url: '/api/register', 
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response); 
                
                        alert('Registration successful!'); 
                    },
                    error: function(xhr, status, error) {
                        console.error(error); 
                        alert('Registration failed: ' + error); 
                    }
                });
            });
        });
    </script>
@endsection
