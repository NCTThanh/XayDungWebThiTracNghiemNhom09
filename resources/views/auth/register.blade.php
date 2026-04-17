@extends('layout')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-header bg-success text-white text-center">
                <h4>ĐĂNG KÝ THÀNH VIÊN</h4>
            </div>
            <div class="card-body">
                {{-- Hiển thị thông báo lỗi nếu có --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/register">
                    @csrf
                    <div class="mb-2">
                        <label>Họ tên:</label>
                        <input name="name" class="form-control" placeholder="Nguyễn Chí Thanh" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-2">
                        <label>Email:</label>
                        <input name="email" type="email" class="form-control" placeholder="thanh@stu.edu.vn" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-2">
                        <label>Mã sinh viên:</label>
                        <input name="student_code" class="form-control" placeholder="DH52201449" required value="{{ old('student_code') }}">
                    </div>
                    <div class="mb-2">
                        <label>Lớp:</label>
                        <input name="class" class="form-control" placeholder="D22_TH01" required value="{{ old('class') }}">
                    </div>
                    <div class="mb-3">
                        <label>Mật khẩu:</label>
                        <input name="password" type="password" class="form-control" placeholder="********" required>
                    </div>
                    <button class="btn btn-success w-100 py-2">ĐĂNG KÝ NGAY</button>
                </form>
                <div class="text-center mt-3">
                    <a href="/login">Đã có tài khoản? Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection