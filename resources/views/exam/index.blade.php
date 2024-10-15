<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="社工模擬考">
    <meta name="author" content="Koie">
    <meta name="generator" content="v1.0">
    <title>社工模擬考</title>
    <link href="{{ asset('exam/assets/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('exam/assets/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="icon" href="{{ asset('exam/img/icon.png') }}">
    <style>
        a.btn {
            width: 100%;
            height: 100px;
            max-height: 100px;
            line-height: 82px;
            font-size: 1.5rem
        }

        .subject {
            padding: 10px
        }
    </style>
    <meta name="theme-color" content="#fc6f5e">
</head>

<body>
    <div class="jumbotron">
        <h1 class="display-4">社工模擬考</h1>
        <p class="lead">歷屆考題 隨機 20 題</p>
        <hr class="my-4">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 subject">
                <a href="{{ url('choose') }}" class="btn btn-info btn-lg">完整題目考試</a>
            </div>
            <div class="col-sm-6 subject">
                <a href="{{ url('exam') }}" class="btn btn-info btn-lg">隨機</a>
            </div>
            @foreach ($subjects as $subject)
                <div class="col-sm-6 subject">
                    <a href="{{ url("exam/{$subject->id}") }}" class="btn btn-info btn-lg">{{ $subject->name }}</a>
                </div>
            @endforeach
        </div>
    </div>

    <script src="{{ asset('exam/assets/jquery.min.js') }}"></script>
    <script src="{{ asset('exam/assets/bootstrap.min.js') }}"></script>
</body>

</html>
