<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    </head>
    <body class="antialiased">
        <form action="{{ route('invite') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <h1>Invite Users</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                    @endif
                    @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                    @endif
                    
                    <div class="form-group">
                        <label for="">Enter Email</label><br>
                        <input type="email" class="form-control" name="email" value="" placeholder="Enter Email">
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info">Invite</button>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>
