@extends('poppy::template.default')
@section('head-css')
    <style>
        a {
            color: #0d95e8;
            text-decoration: none;
        }

        .container .md {
            min-width: 380px;
            width: 60%;
            margin: 0 auto;
        }

        .panel {
            margin-bottom: 20px;
            background-color: #ffffff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05)
        }

        .panel-body {
            padding: 15px
        }

        .panel-heading {
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px
        }

        .panel-title {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 16px;
            color: inherit
        }

        .panel-default > .panel-heading {
            color: #333333;
            background-color: #f5f5f5;
            border-color: #dddddd
        }

        .panel-primary > .panel-heading {
            color: #ffffff;
            background-color: #337ab7;
            border-color: #337ab7
        }

        .panel-success {
            border-color: #d6e9c6
        }

        .panel-success > .panel-heading {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6
        }

        .panel-info > .panel-heading {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1
        }

        .panel-warning {
            border-color: #faebcc
        }

        .panel-warning > .panel-heading {
            color: #8a6d3b;
            background-color: #fcf8e3;
            border-color: #faebcc
        }
    </style>
@endsection
@section('body-main')
    <div class="container">
        <div class="md">
            <div class="panel @if ( $code === 0 )  panel-success @else panel-warning  @endif">
                <div class="panel-heading">
                    <h3 class="panel-title">提示</h3>
                </div>
                <div class="panel-body">
                    <p>{!! $message !!}</p>
                    @if ($to)
                        <p>
                            @if($time)您将在 <span id="clock">{{ (int) ($time / 1000) }}</span>秒内跳转至目标页面, 如果不想等待, @endif
                            <a href="{!! $to !!}">点此立即跳转</a>
                        </p>
                        @if($time)
                            <script>
                                $(function () {
                                    let t = {!! $time !!};//设定跳转的时间
                                    setInterval(function () {
                                        if (t === 0) {
                                            window.location.href = "{!! $to !!}"; //设定跳转的链接地址
                                        }
                                        document.getElementById('clock').innerText = Math.ceil(t / 1000);// 显示倒计时
                                        t -= 1000;
                                    }, 1000); //启动1秒定时
                                })
                            </script>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection