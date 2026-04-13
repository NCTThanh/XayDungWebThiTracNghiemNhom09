<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị hệ thống') - QuizPro</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item-active {
            background-color: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            border-right: 4px solid #4f46e5;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 flex flex-col">
        <div class="flex items-center justify-center h-20 border-b bg-indigo-600 text-white">
            <span class="text-3xl font-bold tracking-wider">QUIZ<span class="text-white/80">PRO</span></span>
        </div>

        <nav class="mt-6 px-3 flex-1 overflow-y-auto">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-chart-line w-6 text-center"></i> <span class="font-medium">Bảng điều khiển</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.quizzes') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.quizzes') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-file-alt w-6 text-center"></i> <span class="font-medium">Quản lý Đề thi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.users') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-users w-6 text-center"></i> <span class="font-medium">Quản lý Sinh viên</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attendance') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.attendance') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-qrcode w-6 text-center"></i> <span class="font-medium">Điểm danh QR</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.results') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.results') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-poll w-6 text-center"></i> <span class="font-medium">Thống kê Kết quả</span>
                    </a>
                </li>
               
                <li>
                    <a href="{{ route('admin.surveys') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-indigo-50 rounded-xl transition {{ request()->routeIs('admin.surveys') ? 'sidebar-item-active' : '' }}">
                        <i class="fas fa-comments w-6 text-center text-yellow-500"></i> <span class="font-medium">Phản hồi Khảo sát</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="ml-64 min-h-screen bg-gray-50 flex flex-col">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-40 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-800">@yield('title', 'Admin Dashboard')</h1>

            <div class="relative">
                <button onclick="toggleDropdown('adminDropdown')" class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 px-3 py-1.5 rounded-xl transition cursor-pointer">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-800">{{ session('admin')->name ?? 'Quản trị viên' }}</p>
                        <p class="text-xs text-indigo-600 capitalize font-medium">{{ session('admin')->role ?? 'Admin' }}</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(session('admin')->name ?? 'A') }}&background=4f46e5&color=fff" 
                         class="w-10 h-10 rounded-full border-2 border-indigo-100 shadow-sm" alt="Avatar">
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>
                    <div id="adminDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-user-circle w-5 text-center mr-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition">
                        <i class="fas fa-key w-5 text-center mr-2"></i> Đổi mật khẩu
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="{{ route('logout') }}" class="block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-bold transition">
                        <i class="fas fa-sign-out-alt w-5 text-center mr-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </header>

        <div class="p-8 flex-1">
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
        </div>
    </main>

    <script>
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('adminDropdown');
            if (!e.target.closest('button[onclick*="adminDropdown"]') && dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>