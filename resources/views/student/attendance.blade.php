@extends('layout')
@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>📋 Lịch Sử Điểm Danh</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="/dashboard" class="btn btn-secondary">← Quay Lại</a>
            <a href="/attendance/scan/new" class="btn btn-success">+ Điểm Danh</a>
        </div>
    </div>

    @if($attendanceRecords && $attendanceRecords->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Đề Thi / Lớp</th>
                    <th>Giờ Điểm Danh</th>
                    <th>Trạng Thái</th>
                    <th>Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceRecords as $record)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $record->session->quiz->title ?? 'N/A' }}</strong>
                        <br>
                        <small class="text-muted">GV: {{ $record->session->creator->name ?? 'N/A' }}</small>
                    </td>
                    <td>
                        @if($record->scan_time)
                            {{ $record->scan_time->format('H:i d/m/Y') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($record->status === 'Present')
                            <span class="badge bg-success">✓ Có Mặt</span>
                        @else
                            <span class="badge bg-danger">✗ {{ $record->status }}</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showDetail('{{ $record->session->quiz->title }}', '{{ $record->scan_time }}', '{{ $record->status }}')">
                            <i class="fas fa-eye"></i> Xem
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($attendanceRecords, 'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $attendanceRecords->links() }}
    </div>
    @endif
    @else
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i> Bạn chưa có lịch sử điểm danh. 
        <a href="/attendance/scan/new" class="alert-link">Bắt đầu điểm danh ngay</a>
    </div>
    @endif

    <!-- Thống kê -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📊 Tổng Lần Điểm Danh</h5>
                    <h3 class="text-primary">{{ $attendanceRecords ? $attendanceRecords->count() : 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">✓ Số Lần Có Mặt</h5>
                    <h3 class="text-success">{{ $attendanceRecords ? $attendanceRecords->where('status', 'Present')->count() : 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📈 Tỷ Lệ Điểm Danh</h5>
                    @php
                        $total = $attendanceRecords ? $attendanceRecords->count() : 0;
                        $present = $attendanceRecords ? $attendanceRecords->where('status', 'Present')->count() : 0;
                        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                    @endphp
                    <h3 class="text-warning">{{ $rate }}%</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Chi tiết điểm danh -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Chi Tiết Điểm Danh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Đề Thi / Lớp</label>
                    <input type="text" class="form-control" id="detailQuiz" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giờ Điểm Danh</label>
                    <input type="text" class="form-control" id="detailTime" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng Thái</label>
                    <input type="text" class="form-control" id="detailStatus" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showDetail(quiz, time, status) {
        document.getElementById('detailQuiz').value = quiz;
        document.getElementById('detailTime').value = time ? new Date(time).toLocaleString('vi-VN') : 'N/A';
        document.getElementById('detailStatus').value = status === 'Present' ? '✓ Có Mặt' : status;
    }
</script>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        padding: 5px 10px;
    }
</style>
@endsection
