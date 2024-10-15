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
    <meta name="theme-color" content="#fc6f5e">
    <style>
        td {
            padding-top: 0 !important;
            padding-bottom: 0 !important
        }

        .title {
            margin: 25px auto;
            text-align: center
        }

        .question {
            margin: 12px 0;
            display: block
        }

        .option {
            padding-bottom: 5px
        }

        .container {
            display: block;
            position: relative;
            margin: 40px auto;
            height: auto;
            padding: 20px;
        }

        .error {
            color: #f00
        }

        input[type=radio] {
            position: absolute;
            visibility: hidden;
        }

        .option label {
            display: block;
            position: relative;
            border: #333 solid .5px;
            border-radius: 10px;
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            z-index: 9;
            cursor: pointer;
            -webkit-transition: all 0.25s linear;
        }

        input[type=radio]:checked~label {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 title">
                <h2>分數： {{ $grade }}</h2>
            </div>
        </div>

        <table class="table table-borderless">
            <tbody>
                @foreach ($results as $key => $result)
                    <tr>
                        <th scope="row">{{ $key + 1 }}</th>
                        <td><label class="question">{{ $result->question }}</label></td>
                    </tr>
                    <tr class="option">
                        <th scope="row"></th>
                        <td>
                            <input type="radio" name="{{ "option[{$result->id}]" }}" value="A"
                                @if ($result->answer === 'A') checked @else disabled @endif>
                            <label for="{{ 'A' . ($key + 1) }}">{{ $result->A }}</label>
                        </td>
                    </tr>
                    <tr class="option">
                        <th scope="row"></th>
                        <td>
                            <input type="radio" name="{{ "option[{$result->id}]" }}" value="B"
                                @if ($result->answer === 'B') checked @else disabled @endif>
                            <label for="{{ 'B' . ($key + 1) }}">{{ $result->B }}</label>
                        </td>
                    </tr>
                    <tr class="option">
                        <th scope="row"></th>
                        <td>
                            <input type="radio" name="{{ "option[{$result->id}]" }}" value="C"
                                @if ($result->answer === 'C') checked @else disabled @endif>
                            <label for="{{ 'C' . ($key + 1) }}">{{ $result->C }}</label>
                        </td>
                    </tr>
                    <tr class="option">
                        <th scope="row"></th>
                        <td>
                            <input type="radio" name="{{ "option[{$result->id}]" }}" value="D"
                                @if ($result->answer === 'D') checked @else disabled @endif>
                            <label for="{{ 'D' . ($key + 1) }}">{{ $result->D }}</label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            <a href="{{ url('/') }}">再次測驗</a>
        </div>
    </div>
    <script src="{{ asset('exam/assets/jquery.min.js') }}"></script>
    <script src="{{ asset('exam/assets/bootstrap.min.js') }}"></script>
</body>

</html>
