<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Sign In | {{ $location['location_name'] }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<!-- Responsive navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand" href="#">
            {{ $location['location_name'] }}
        </span>
    </div>
</nav>
<!-- Page content-->
<div class="container">
    <div class="text-center mt-5">
        <h1>Welcome To {{ $location['location_name'] }}</h1>
        {{--<p class="lead">A complete project boilerplate built with Bootstrap</p>--}}
        {{--<p>Bootstrap v5.1.3</p>--}}


        <div class="mt-5">


            <form method="post" action="{{ route('public.enter', [$location['uuid']]) }}">

                @csrf

                <div class="form-group my-2">
                    <input type="text" class="form-control" id="first_name" name="first_name"  placeholder="First Name">
                </div>

                <div class="form-group my-2">
                    <input type="text" class="form-control" id="last_name" name="last_name"  placeholder="Last Name">
                </div>

                <div class="form-group my-2">
                    <input type="email" class="form-control" id="email"  name="email" placeholder="Email">
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li> {{ $error }} </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-5">
                    <button type="submit" class="btn btn-dark">Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
