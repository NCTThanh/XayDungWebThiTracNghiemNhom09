<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang Sinh Viên') - QuizPro</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-indigo-600 text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-2xl font-bold tracking-wider">
                <i class="fas fa-graduation-cap"></i> QUIZ<span class="text-indigo-200">PRO</span>
            </a>

            <div class="relative">
                <button onclick="toggleDropdown('userDropdown')" class="flex items-center gap-3 focus:outline-none hover:bg-indigo-700 px-3 py-1.5 rounded-xl transition cursor-pointer">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-white">{{ Auth::user()->name ?? session('user')->name ?? 'Sinh viên' }}</p>
                        <p class="text-xs text-indigo-200 font-medium">MSV: {{ Auth::user()->student_code ?? session('user')->student_code ?? 'N/A' }}</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'SV') }}&background=fff&color=4f46e5" 
                         class="w-10 h-10 rounded-full border-2 border-indigo-300 shadow-sm" alt="Avatar">
                    <i class="fas fa-chevron-down text-indigo-300 text-xs"></i>
                </button>

                <div id="userDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                    <a href="{{ route('user.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-user-circle w-5 text-center mr-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="{{ route('exam.history') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-history w-5 text-center mr-2"></i> Lịch sử thi
                    </a>
                    <a href="{{ route('attendance.history') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-qrcode w-5 text-center mr-2"></i> Lịch sử điểm danh
                    </a>
                    <a href="/survey" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-poll w-5 text-center mr-2 text-gray-400"></i> Khảo sát hệ thống
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="{{ route('logout') }}" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-bold transition">
                        <i class="fas fa-sign-out-alt w-5 text-center mr-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 min-h-[calc(100vh-4rem)]">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl flex items-center shadow-sm">
                <i class="fas fa-check-circle text-xl mr-3"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl flex items-center shadow-sm">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i> {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl shadow-sm">
                <div class="font-bold mb-2 flex items-center"><i class="fas fa-exclamation-circle mr-2 text-lg"></i> Vui lòng kiểm tra lại:</div>
                <ul class="list-disc list-inside space-y-1 ml-2 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            if (!e.target.closest('button[onclick*="userDropdown"]') && dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>