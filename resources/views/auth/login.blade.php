@extends('layout')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-white"><h4>Đăng Nhập</h4></div>
            <div class="card-body">
                <form method="POST" action="/login">
                    @csrf
                    <div class="mb-3"><input name="email" class="form-control" placeholder="Email / Username Admin" required></div>
                    <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Mật khẩu" required></div>
                    <button class="btn btn-primary w-100">Đăng Nhập</button>
                    <a href="/register" class="d-block text-center mt-3">Chưa có tài khoản? Đăng ký</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection