<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" 
              integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" 
              crossorigin="anonymous">
        <style>
            <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
            <div class="top-right links">
                @auth
                <a href="{{ url('/home') }}">Home</a>
                @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
            @endif

            <div class="content">
                @if(isset($message))
                    {{ $message }}
                @endif
                <form action="{{ route('lot.store') }}" method="POST" class="currency-form">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group">
                        <label for="title_input">Currency</label>
                        <input type="text" class="form-control {{ $errors->first('currency') ? 'is-invalid' : '' }}" id="currency_input" name="currency"  value="{{ old('currency') }}" placeholder="Enter currency">
                        @if ($errors->has('currency'))
                        <span class="text-danger">{{ $errors->first('currency')  }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="short_name_input">Price</label>
                        <input type="text" class="form-control {{ $errors->first('price') ? 'is-invalid' : '' }}" id="price_input" name="price"  value="{{ old('price') }}" placeholder="Enter price">
                        @if ($errors->has('price'))
                        <span class="text-danger">{{ $errors->first('price')  }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="logo_url_input">Date open</label>
                        <input type="text" class="form-control {{ $errors->first('date_open') ? 'is-invalid' : '' }}"  id="date_open_input" name="date_open"  value="{{ old('date_open') }}" placeholder="d/m/Y H:i">
                        @if ($errors->has('date_close'))
                        <span class="text-danger">{{ $errors->first('date_open')  }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="price_input">Date close</label>
                        <input type="text" class="form-control {{ $errors->first('date_close') ? 'is-invalid' : '' }}"  id="date_close_input" name="date_close"  value="{{ old('date_close') }}" placeholder="d/m/Y H:i">
                        @if ($errors->has('date_close'))
                        <span class="text-danger">{{ $errors->first('date_close')  }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Save</button>
                </form>
            </div>
        </div>
    </body>
</html>
