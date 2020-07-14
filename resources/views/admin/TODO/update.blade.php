@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @include('flash::message')
            @include('admin.TODO.validator')
        </div>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">代表信息修改</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="">
                    {{ csrf_field() }}
                        <div class="form-group">
                        <label for="name" class="col-md-4 control-label">代表姓名</label>
                        <div class="col-md-6">
                            <input id="file"  class="form-control" name="behalf[name]"
                                   value="{{ old('behalf')['name'] ? old('behalf')['name'] : $behalf->name }}"
                                   required autofocus>
                        </div>
                        </div>

                        <div class="form-group">
                        <label for="num" class="col-md-4 control-label">代表学号</label>
                        <div class="col-md-6">
                            <input id="file"  class="form-control" name="behalf[student_id]"
                                   value="{{ old('behalf')['student_id'] ? old('behalf')['student_id'] : $behalf->student_id }}"
                                   required autofocus>
                        </div>
                        </div>

                        <div class="form-group">
                        <label for="stuid" class="col-md-4 control-label">是否签到</label>
                            @foreach($behalf->sign() as $ind=>$val)
                            <label class="radio-inline">
                            <input type="radio" name="behalf[is_sign]"
                                   {{ isset($behalf->is_sign) && $behalf->is_sign == $ind ? 'checked' : ''  }}
                                   value="{{$ind}}">{{$val}}
                            </label>
                                @endforeach
                        </div>

                        <div class="form-group">
                            <label for="stuid" class="col-md-4 control-label">是否投票</label>
                            @foreach($behalf->vote() as $ind=>$val)
                            <label class="radio-inline">
                                <input type="radio" name="behalf[is_vote]"
                                       {{ isset($behalf->is_vote) && $behalf->is_vote == $ind ? 'checked' : ''  }}
                                       value="{{$ind}}">{{$val}}
                            </label>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" name="sub" class="btn btn-primary">提交</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection