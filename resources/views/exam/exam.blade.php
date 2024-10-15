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
                <h2>開始測驗</h2>
            </div>
        </div>
        <form id="exam-form" action="{{ url('exam') }}" method="post">
            @csrf
            <table class="table table-borderless">
                <tbody>
                    @foreach ($questions as $key => $question)
                        <tr>
                            <th scope="row">{{ $key + 1 }}</th>
                            <td><label class="question">{{ $question->question }}</label></td>
                        </tr>
                        <tr class="option">
                            <th scope="row"></th>
                            <td>
                                <input type="radio" id="{{ 'A' . ($key + 1) }}" class="required"
                                    name="{{ "option[{$question->id}]" }}" value="A" required>
                                <label for="{{ 'A' . ($key + 1) }}">{{ $question->A }}</label>
                            </td>
                        </tr>
                        <tr class="option">
                            <th scope="row"></th>
                            <td>
                                <input type="radio" id="{{ 'B' . ($key + 1) }}"
                                    name="{{ "option[{$question->id}]" }}" value="B">
                                <label for="{{ 'B' . ($key + 1) }}">{{ $question->B }}</label>
                            </td>
                        </tr>
                        <tr class="option">
                            <th scope="row"></th>
                            <td>
                                <input type="radio" id="{{ 'C' . ($key + 1) }}"
                                    name="{{ "option[{$question->id}]" }}" value="C">
                                <label for="{{ 'C' . ($key + 1) }}">{{ $question->C }}</label>
                            </td>
                        </tr>
                        <tr class="option">
                            <th scope="row"></th>
                            <td>
                                <input type="radio" id="{{ 'D' . ($key + 1) }}"
                                    name="{{ "option[{$question->id}]" }}" value="D">
                                <label for="{{ 'D' . ($key + 1) }}">{{ $question->D }}</label>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row">
                <div class="col-sm-12 title">
                    <button type="submit" class="btn btn-primary">交卷</button>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('exam/assets/jquery.min.js') }}"></script>
    <script src="{{ asset('exam/assets/bootstrap.min.js') }}"></script>
    <script src="{{ asset('exam/assets/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('exam/assets/jquery-validate/messages_zh_TW.min.js') }}"></script>
    <script src="{{ asset('exam/assets/swal2/swal2.min.js') }}"></script>
    <script>
        $('#exam-form').on('submit', function(form) {

            $('#exam-form').validate({
                focusInvalid: true,
                errorPlacement: function(error, element) {
                    error.appendTo(element.parent().parent().prev().find('td'));
                }
            });

            if ($('#exam-form').valid()) {
                form.submit();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '錯誤',
                    text: '有題目沒有作答，請完整作答'
                })
            }
        });
    </script>
</body>

</html>
