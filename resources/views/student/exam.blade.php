@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 pb-12">
    <div class="sticky top-0 z-50 bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h1>
                <p class="text-sm text-gray-500">Thí sinh: {{ Auth::user()->name }} - MSV: {{ Auth::user()->student_code }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div id="timer-box" class="bg-red-50 border border-red-200 px-4 py-2 rounded-lg">
                    <span class="text-xs text-red-500 block uppercase font-bold">Thời gian còn lại</span>
                    <span id="countdown" class="text-2xl font-mono font-bold text-red-600">00:00</span>
                </div>
                <button type="button" onclick="confirmSubmit()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-md">
                    Nộp bài
                </button>
            </div>
        </div>
        <div class="w-full bg-gray-200 h-1.5">
            <div id="progress-bar" class="bg-green-500 h-1.5 transition-all duration-500" style="width: 0%"></div>
        </div>
    </div>

    <div class="container mx-auto px-4 mt-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3 space-y-6">
            <form id="exam-form" action="{{ route('exam.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                
                @foreach($questions as $index => $q)
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6 question-card border border-transparent transition-all" id="q-card-{{ $index + 1 }}">
                    <div class="flex justify-between mb-4">
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold">
                            Câu {{ $index + 1 }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg text-gray-800 mb-6 font-medium leading-relaxed">{{ $q->question }}</h3>

                    <div class="space-y-3">
                        @foreach($q->options as $option)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-indigo-50 transition border-gray-200 group">
                            <input type="radio" 
                                   name="answers[{{ $q->id }}]" 
                                   value="{{ $option->id }}"
                                   onchange="updateProgress({{ $index + 1 }})"
                                   class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-4 text-gray-700 group-hover:text-indigo-700">{{ $option->option_text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </form>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-28 border border-gray-100">
                <h2 class="text-sm font-bold text-gray-500 uppercase mb-4 text-center">Bảng câu hỏi</h2>
                <div class="grid grid-cols-5 gap-2">
                    @foreach($questions as $index => $q)
                    <a href="#q-card-{{ $index + 1 }}" 
                       id="nav-q-{{ $index + 1 }}"
                       class="w-full aspect-square flex items-center justify-center border rounded-md text-sm font-bold hover:bg-indigo-50 transition text-gray-400 border-gray-200">
                        {{ $index + 1 }}
                    </a>
                    @endforeach
                </div>
                <div class="mt-6 pt-6 border-t space-y-2">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <div class="w-3 h-3 bg-indigo-500 rounded-sm"></div> Đã trả lời
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <div class="w-3 h-3 border border-gray-200 rounded-sm"></div> Chưa trả lời
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cheat-modal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center shadow-2xl">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">CẢNH BÁO!</h2>
        <p class="text-gray-600 mb-6 font-medium">Bạn vừa rời khỏi trang làm bài. Hành vi này đã được ghi lại và gửi tới giáo viên!</p>
        <button onclick="closeCheatModal()" class="bg-red-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-700 transition w-full">Quay lại làm bài</button>
    </div>
</div>

<script>
    let isSafeExit = false;
    let timeLeft = {{ $quiz->duration * 60 }};
    const totalQuestions = {{ count($questions) }};

    // Đếm ngược thời gian
    const timer = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timer);
            isSafeExit = true;
            document.getElementById('exam-form').submit();
        } else {
            timeLeft--;
            let min = Math.floor(timeLeft / 60);
            let sec = timeLeft % 60;
            document.getElementById('countdown').textContent = `${min < 10 ? '0':''}${min}:${sec < 10 ? '0':''}${sec}`;
            if (timeLeft < 60) document.getElementById('timer-box').classList.add('animate-pulse', 'bg-red-100');
        }
    }, 1000);

    // Cập nhật tiến độ & đổi màu bảng câu hỏi
    function updateProgress(index) {
        // Đổi màu ô số bên phải
        const navBtn = document.getElementById('nav-q-' + index);
        if (navBtn) {
            navBtn.classList.remove('text-gray-400', 'border-gray-200');
            navBtn.classList.add('bg-indigo-500', 'text-white', 'border-indigo-500');
        }
        
        // Cập nhật thanh Progress bar
        const answered = document.querySelectorAll('input[type="radio"]:checked').length;
        document.getElementById('progress-bar').style.width = (answered / totalQuestions * 100) + '%';
    }

    // Chống gian lận
    window.onblur = function() {
        if (!isSafeExit) {
            document.getElementById('cheat-modal').classList.remove('hidden');
            fetch('{{ route("exam.log-cheat") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'tab_switch' })
            });
        }
    };

    function closeCheatModal() { document.getElementById('cheat-modal').classList.add('hidden'); }

    function confirmSubmit() {
        Swal.fire({
            title: 'Nộp bài thi?',
            text: "Hệ thống sẽ tính điểm dựa trên các câu đã trả lời.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Vâng, nộp bài!',
            cancelButtonText: 'Kiểm tra lại'
        }).then((result) => {
            if (result.isConfirmed) {
                isSafeExit = true;
                document.getElementById('exam-form').submit();
            }
        });
    }

    window.onbeforeunload = function() {
        if (!isSafeExit) return "Bài làm chưa được lưu!";
    };
    
    // Thẻ VIP cho nút thoát
    document.querySelectorAll('a[href="/logout"]').forEach(btn => {
        btn.onclick = () => isSafeExit = true;
    });
</script>
@endsection