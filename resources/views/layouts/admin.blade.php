<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Quiz System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item-active {
            background-color: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            border-right: 4px solid #4f46e5;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 overflow-x-hidden">

    <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 transition-all duration-300 transform md:translate-x-0 -translate-x-full" id="sidebar">
        <div class="flex items-center justify-center h-20 border-b">
            <span class="text-2xl font-bold text-indigo-600 tracking-wider">QUIZ<span class="text-gray-800">PRO</span></span>
        </div>

        <nav class="mt-6">
            <div class="px-4 space-y-2">
                <a href="/admin" class="flex items-center px-4 py-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition {{ request()->is('admin') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-chart-line w-6"></i>
                    <span class="ml-3 font-medium">Bảng điều khiển</span>
                </a>

                <a href="/admin/quiz" class="flex items-center px-4 py-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition {{ request()->is('admin/quiz*') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-file-alt w-6"></i>
                    <span class="ml-3 font-medium">Quản lý đề thi</span>
                </a>

                @if(session('admin') && session('admin')->role == 'admin')
                <a href="/admin/users" class="flex items-center px-4 py-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition {{ request()->is('admin/users*') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-users w-6"></i>
                    <span class="ml-3 font-medium">Quản lý sinh viên</span>
                </a>
                @endif

                <a href="{{ route('admin.surveys') }}" class="flex items-center px-4 py-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition {{ request()->routeIs('admin.surveys') ? 'sidebar-item-active' : '' }}">
    <i class="fas fa-poll w-6"></i>
    <span class="ml-3 font-medium">Kết quả khảo sát</span>
</a>

                <div class="pt-4 pb-2 border-t mt-4">
                    <span class="px-4 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Hệ thống</span>
                </div>

                <a href="/logout" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-lg transition">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span class="ml-3 font-medium">Đăng xuất</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-40">
            <button class="md:hidden text-gray-600 text-2xl" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <div class="relative group">
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-800">{{ session('admin')->name ?? 'Quản trị viên' }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">{{ session('admin')->role ?? 'Admin' }}</p>
                    </div>
                    <img class="h-10 w-10 rounded-full border-2 border-indigo-500 p-0.5" src="https://ui-avatars.com/api/?name={{ session('admin')->name ?? 'A' }}&background=6366f1&color=fff" alt="Avatar">
                </div>
            </div>
        </header>

        <div class="p-8 flex-1">
            @if(session('success'))
            <div class="mb-6 flex items-center p-4 text-green-800 rounded-lg bg-green-50 border border-green-200 animate-fade-in" role="alert">
                <i class="fas fa-check-circle mr-3"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 flex items-center p-4 text-red-800 rounded-lg bg-red-50 border border-red-200 animate-fade-in" role="alert">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
            @endif

            @yield('content')
        </div>

        <footer class="bg-white border-t py-4 px-8 text-center text-sm text-gray-500">
            &copy; 2026 QuizPro System. Phát triển bởi Senior Fullstack Team.
        </footer>
    </main>

    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden" id="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Tự động ẩn thông báo sau 5 giây
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>