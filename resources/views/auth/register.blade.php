@extends('layout')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="/register">
                    @csrf
                    <input name="name" class="form-control mb-2" placeholder="Họ tên" required>
                    <input name="email" class="form-control mb-2" placeholder="Email" required>
                    <input name="student_code" class="form-control mb-2" placeholder="Mã SV" required>
                    <input name="class" class="form-control mb-2" placeholder="Lớp" required>
                    <input name="password" type="password" class="form-control mb-3" placeholder="Mật khẩu" required>
                    <button class="btn btn-success w-100">Đăng Ký</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection