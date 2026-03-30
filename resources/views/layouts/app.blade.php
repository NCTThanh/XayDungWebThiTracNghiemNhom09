<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Hệ thống Thi Trắc Nghiệm - Sinh Viên</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col">

    <nav class="bg-indigo-600 text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            
            <a href="/dashboard" class="text-xl font-bold flex items-center hover:text-indigo-200 transition">
                <div class="w-8 h-8 bg-white text-indigo-600 rounded-full flex items-center justify-center mr-3 shadow-sm">
                    <i class="fas fa-graduation-cap text-sm"></i>
                </div>
                QuizPro STU
            </a>

            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center text-indigo-100 text-sm bg-indigo-700 px-3 py-1.5 rounded-full">
                    <i class="fas fa-user-circle mr-2 text-lg"></i> 
                    {{ auth()->user()->name ?? 'Sinh viên' }} 
                    <span class="ml-2 pl-2 border-l border-indigo-500 font-bold">
                        {{ auth()->user()->student_code ?? '' }}
                    </span>
                </div>
                
                <a href="/logout" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition shadow flex items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i> Thoát
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t py-4 text-center text-sm text-gray-500 mt-auto">
        &copy; {{ date('Y') }} Hệ thống quản lý thi trắc nghiệm. Developed for STU.
    </footer>

</body>
</html>