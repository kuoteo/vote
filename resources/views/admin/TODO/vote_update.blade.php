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
                    <div class="panel-heading">候选人信息修改</div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="num" class="col-md-4 control-label">代表编号</label>
                                <div class="col-md-6">
                                    <input id="file"  class="form-control" name="Vote[vote_id]"
                                           value="{{ old('Vote')['vote_id'] ? old('Vote')['vote_id'] : $Vote->vote_id }}"
                                           required autofocus>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-md-4 control-label">候选人姓名</label>
                                <div class="col-md-6">
                                    <input id="file"  class="form-control" name="Vote[name]"
                                           value="{{ old('Vote')['name'] ? old('Vote')['name'] : $Vote->name }}"
                                           required autofocus>
                                </div>
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