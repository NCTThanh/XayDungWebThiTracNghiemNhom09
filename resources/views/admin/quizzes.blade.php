@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Quản lý Đề thi</h1>

    <div class="bg-white p-6 rounded-xl shadow-sm mb-8 border border-gray-100">
        <h2 class="text-sm font-semibold text-gray-500 uppercase mb-4">Tạo đề thi & Tự động Random câu hỏi</h2>
        <form method="POST" action="{{ route('admin.quiz.store') }}" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs text-gray-500 font-bold mb-1">Tên đề thi mới *</label>
                <input name="title" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ví dụ: Thi Cuối Kỳ 2026" required>
            </div>
            
            <div class="w-full md:w-32">
                <label class="block text-xs text-gray-500 font-bold mb-1">Thời gian *</label>
                <div class="relative">
                    <input name="duration" type="number" class="w-full border-gray-300 rounded-lg shadow-sm pr-12" placeholder="Phút" required>
                    <span class="absolute right-3 top-2 text-gray-400 text-sm">phút</span>
                </div>
            </div>

            <div class="w-full md:w-48">
                <label class="block text-xs text-indigo-500 font-bold mb-1">Random bao nhiêu câu?</label>
                <div class="relative">
                    <input name="random_count" type="number" min="0" value="0" class="w-full border-indigo-300 bg-indigo-50 rounded-lg shadow-sm pr-10 focus:ring-indigo-500">
                    <span class="absolute right-3 top-2 text-indigo-400 text-sm">câu</span>
                </div>
            </div>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-md h-[42px] flex items-center">
                <i class="fas fa-magic mr-2"></i> Tạo Đề
            </button>
        </form>
        <p class="text-[11px] text-gray-400 mt-2 italic">* Để số 0 nếu bạn muốn tự thêm câu hỏi thủ công sau.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Đề thi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Thời gian</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Hiển thị điểm</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($quizzes as $q)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $q->title }}</div>
                        <div class="text-xs text-gray-400">{{ $q->questions_count ?? 0 }} câu hỏi</div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm">{{ $q->duration }} phút</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.quiz.toggle-score', $q->id) }}" method="POST">
                            @csrf
                            @php $isHidden = Cache::get('hide_score_'.$q->id, false); @endphp
                            <button class="text-[10px] px-2 py-1 rounded-full border {{ $isHidden ? 'bg-red-50 text-red-600 border-red-200' : 'bg-green-50 text-green-600 border-green-200' }}">
                                {{ $isHidden ? 'Đang Ẩn' : 'Đang Hiện' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.questions', $q->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded" title="Quản lý Câu hỏi"><i class="fas fa-list-ul"></i></a>
                        
                        <button onclick="openEditModal({{ $q->id }}, '{{ $q->title }}', {{ $q->duration }})" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded" title="Sửa Đề thi">
                            <i class="fas fa-edit"></i>
                        </button>

                        <a href="{{ route('admin.quiz.export', $q->id) }}" class="text-green-600 hover:text-green-900 bg-green-50 p-2 rounded" title="Xuất Excel"><i class="fas fa-file-excel"></i></a>

                        <form action="{{ route('admin.quiz.destroy', $q->id) }}" method="POST" class="inline delete-form">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700 bg-red-50 p-2 rounded" title="Xóa Đề thi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-[60] hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-xl font-bold mb-4">Chỉnh sửa đề thi</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên đề thi</label>
                <input type="text" name="title" id="editTitle" class="w-full border-gray-300 rounded-lg" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Thời gian (phút)</label>
                <input type="number" name="duration" id="editDuration" class="w-full border-gray-300 rounded-lg" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-500 font-medium hover:bg-gray-100 rounded-lg transition">Hủy</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, title, duration) {
        document.getElementById('editTitle').value = title;
        document.getElementById('editDuration').value = duration;
        document.getElementById('editForm').action = `/admin/quiz/${id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDelete(button) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Tất cả câu hỏi và kết quả của đề thi này sẽ bị xóa sạch!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Vâng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
@endsection