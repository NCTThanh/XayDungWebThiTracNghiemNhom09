@extends('layout')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Xin chào, {{ Auth::user()->name }}</h3>
    <div>
        <a href="/history" class="btn btn-info text-white">Lịch sử thi</a>
        @if(Auth::user()->survey_done == 0)
        <a href="/survey" class="btn btn-warning">Làm Khảo Sát</a>
        @endif
    </div>
</div>
<div class="row">
    @foreach($quizzes as $q)
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5>{{ $q->title }}</h5>
                <p>Thời gian: {{ $q->duration }} phút</p>
                <a href="/exam/{{ $q->id }}" class="btn btn-primary w-100">Vào Thi</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection