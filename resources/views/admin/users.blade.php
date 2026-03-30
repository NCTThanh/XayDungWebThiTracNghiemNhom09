@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <h3 class="text-red-800 font-bold">Thao tác thất bại! Vui lòng kiểm tra lại:</h3>
            </div>
            <ul class="list-disc list-inside text-red-600 text-sm ml-8">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý Sinh viên</h1>
        <button onclick="openAddModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold transition shadow-sm flex items-center">
            <i class="fas fa-plus mr-2"></i> Thêm Sinh viên
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Mã Sinh Viên</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Họ và Tên</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $u)
                    @if($u->role == 'student')
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">#{{ $u->id }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-indigo-600">{{ $u->student_code ?? 'Chưa cập nhật' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $u->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $u->email }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <button onclick="openEditModal({{ $u->id }}, '{{ $u->student_code }}', '{{ $u->name }}', '{{ $u->email }}')" class="text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg text-sm transition" title="Sửa thông tin">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1.5 rounded-lg text-sm transition" title="Xóa sinh viên">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        
        @if($users->where('role', 'student')->isEmpty())
        <div class="py-12 text-center text-gray-500">
            Chưa có sinh viên nào đăng ký trong hệ thống.
        </div>
        @endif
    </div>
</div>

<div id="addModal" class="fixed inset-0 z-[60] hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Thêm Sinh Viên Mới</h3>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Mã Sinh Viên *</label>
                    <input type="text" name="student_code" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="VD: DH520xxxxx" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Họ và Tên *</label>
                    <input type="text" name="name" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="Nguyễn Văn A" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Email đăng nhập *</label>
                    <input type="email" name="email" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="email@stu.edu.vn" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Mật khẩu *</label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="Ít nhất 6 ký tự" required minlength="6">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg transition">Hủy</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition">Thêm ngay</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-[60] hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Chỉnh Sửa Thông Tin</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Mã Sinh Viên *</label>
                    <input type="text" name="student_code" id="editCode" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Họ và Tên *</label>
                    <input type="text" name="name" id="editName" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Email *</label>
                    <input type="email" name="email" id="editEmail" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700">Mật khẩu mới <span class="text-xs text-gray-400 font-normal">(Bỏ trống nếu không đổi)</span></label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500" placeholder="Nhập pass mới nếu muốn đổi">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg transition">Hủy</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Xử lý Modal Thêm
    function openAddModal() { document.getElementById('addModal').classList.remove('hidden'); }
    function closeAddModal() { document.getElementById('addModal').classList.add('hidden'); }

    // Xử lý Modal Sửa
    function openEditModal(id, code, name, email) {
        document.getElementById('editCode').value = code;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editForm').action = `/admin/users/${id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }

    // Xác nhận Xóa bằng SweetAlert
    function confirmDelete(button) {
        Swal.fire({
            title: 'Xóa sinh viên này?',
            text: "Toàn bộ điểm thi của sinh viên sẽ bị mất vĩnh viễn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Vâng, Xóa!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        })
    }
</script>
@endsection