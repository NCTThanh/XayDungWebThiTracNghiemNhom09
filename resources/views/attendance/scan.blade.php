@extends('layouts.app')

@section('title', 'Điểm Danh QR')

@section('content')
<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-6 text-white text-center">
            <h2 class="text-2xl font-bold">📷 Điểm Danh Lớp Học</h2>
            <p class="mt-1 text-sm" id="statusText">Đang xử lý thông tin...</p>
        </div>

        <div class="p-6" id="scannerContainer">
            <div id="reader" style="min-height: 380px;" class="rounded-2xl overflow-hidden border-4 border-gray-300"></div>
        </div>

        <div id="result" class="hidden p-6 border-t bg-gray-50">
            <div id="success" class="hidden text-center">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-green-600">Thành công!</h3>
                <p id="successMsg" class="mt-2 text-gray-800 font-medium"></p>
                
                <a href="{{ route('dashboard') }}" class="mt-6 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg">
                    <i class="fas fa-home mr-2"></i> Quay về Trang chủ
                </a>
            </div>
            <div id="error" class="hidden text-center">
                <i class="fas fa-times-circle text-6xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-red-600" id="errorTitle"></h3>
                <p id="errorMsg" class="text-gray-600 mt-2"></p>
                <button onclick="window.location.reload()" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-lg transition">Thử lại</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scanner;
    
    // Lấy token từ URL do Controller truyền sang (nếu quét bằng Zalo hoặc Camera điện thoại)
    const urlToken = "{{ $token ?? '' }}";

    function startScanner() {
        document.getElementById('statusText').innerText = "Đưa camera vào mã QR của giáo viên";
        scanner = new Html5Qrcode("reader");

        scanner.start(
            { facingMode: "environment" },
            { fps: 12, qrbox: { width: 260, height: 260 } },
            (decodedText) => {
                scanner.stop();
                document.getElementById('scannerContainer').classList.add('hidden');
                document.getElementById('statusText').innerText = "Đang gửi dữ liệu điểm danh...";
                submitAttendance(decodedText);
            }
        ).catch(err => {
            console.error(err);
            document.getElementById('statusText').innerText = "Không thể mở Camera. Vui lòng cấp quyền!";
        });
    }

    function submitAttendance(rawText) {
        // CẮT RÂU RIA: Nếu chuỗi quét được là cả đường link dài, chỉ lấy đoạn mã cuối cùng
        let finalToken = rawText;
        if(finalToken.includes('/')) {
            finalToken = finalToken.split('/').pop();
        }

        // Lấy Token bảo mật CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if(!csrfToken) {
            alert("Lỗi bảo mật: Thiếu CSRF Token trong layout!");
            return;
        }

        fetch('/attendance/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ qr_token: finalToken })
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('result').classList.remove('hidden');
            document.getElementById('statusText').innerText = "Hoàn tất!";
            
            if (data.success) {
                document.getElementById('success').classList.remove('hidden');
                document.getElementById('successMsg').textContent = data.message;
            } else {
                document.getElementById('error').classList.remove('hidden');
                document.getElementById('errorTitle').textContent = "Thất bại";
                document.getElementById('errorMsg').textContent = data.message;
            }
        })
        .catch((error) => {
            console.error(error);
            alert("Lỗi kết nối đến máy chủ! Vui lòng thử lại.");
        });
    }

    window.onload = () => {
        if (urlToken !== '') {
            // NẾU CÓ SẴN TOKEN TỪ URL: Sinh viên vừa quét QR bằng Zalo xong
            // -> Ẩn camera đi và gọi lệnh gửi điểm danh lên Server luôn!
            document.getElementById('scannerContainer').classList.add('hidden');
            submitAttendance(urlToken);
        } else {
            // Nếu không có token (Sinh viên bấm nút trên menu) -> Mở camera lên
            startScanner();
        }
    };
</script>
@endsection