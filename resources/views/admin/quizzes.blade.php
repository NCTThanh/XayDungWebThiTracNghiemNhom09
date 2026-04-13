@extends('layouts.admin')

@section('title', 'Quản lý đề thi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý Đề Thi</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.ai-generate') }}" 
               class="bg-purple-600 text-white px-5 py-2.5 rounded-xl hover:bg-purple-700 transition flex items-center gap-2">
                <i class="fas fa-magic"></i> Phát sinh bằng AI
            </a>
            <button onclick="document.getElementById('createQuizModal').classList.toggle('hidden')" 
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tạo đề thi mới
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">ID</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Tên đề thi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Thời gian (phút)</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Câu hỏi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Lượt thi</th>
                    <th class="px-6 py-4 text-left text-sm font-medium text-gray-500">Điểm Pass</th>
                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-500">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($quizzes as $quiz)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">#{{ $quiz->id }}</td>
                    <td class="px-6 py-4 font-medium">{{ $quiz->title }}</td>
                    <td class="px-6 py-4">{{ $quiz->duration }} phút</td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                            {{ $quiz->questions_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                            {{ $quiz->exam_attempts_count ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $quiz->pass_score ?? 5.0 }}/10</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.quiz.questions', $quiz->id) }}" 
                               class="text-indigo-600 hover:text-indigo-800 text-xs" title="Quản lý câu hỏi">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.quiz.results', $quiz->id) }}" 
                               class="text-green-600 hover:text-green-800 text-xs" title="Xem kết quả">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <button onclick="editQuiz({{ $quiz->id }}, '{{ $quiz->title }}', {{ $quiz->duration }}, {{ $quiz->pass_score ?? 5.0 }})" 
                                    class="text-yellow-600 hover:text-yellow-800 text-xs" title="Sửa">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Xóa đề thi này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-500">
                        Chưa có đề thi nào. Hãy tạo đề thi mới!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tạo đề thi -->
<div id="createQuizModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-8 w-full max-w-2xl max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-6 sticky top-0 bg-white pt-0">Tạo đề thi mới</h3>
        <form action="{{ route('admin.quizzes.store') }}" method="POST" id="createQuizForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên đề thi</label>
                <input type="text" name="title" class="w-full border rounded-xl px-4 py-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea name="description" class="w-full border rounded-xl px-4 py-3" rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Thời gian làm bài (phút)</label>
                    <input type="number" name="duration" value="45" class="w-full border rounded-xl px-4 py-3" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Điểm pass (0-10)</label>
                    <input type="number" name="pass_score" value="5.0" step="0.5" class="w-full border rounded-xl px-4 py-3" required>
                </div>
            </div>

            <!-- Question Bank Section -->
            <div class="mb-6 border-t pt-4">
                <h4 class="font-semibold text-gray-700 mb-3">Chọn câu hỏi từ ngân hàng</h4>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Chủ đề</label>
                    <select id="subjectSelect" class="w-full border rounded-xl px-4 py-2" onchange="loadQuestionsBySubject()">
                        <option value="">-- Chọn chủ đề --</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->questions_count ?? 0 }} câu)</option>
                        @endforeach
                    </select>
                </div>

                <div id="questionContainer" class="hidden mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium">Câu hỏi khả dụng</label>
                        <button type="button" onclick="selectAllQuestions()" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Chọn tất cả
                        </button>
                    </div>
                    <div id="questionList" class="border rounded-xl p-3 max-h-64 overflow-y-auto bg-gray-50">
                    </div>
                </div>

                <div id="selectedQuestionsContainer" class="hidden">
                    <label class="block text-sm font-medium mb-2">Câu hỏi đã chọn: <span id="selectedQuestionsCount">0</span></label>
                    <div id="selectedQuestionsList" class="border rounded-xl p-3 bg-blue-50 max-h-48 overflow-y-auto">
                        <p class="text-gray-500 text-sm">Chưa chọn câu hỏi nào</p>
                    </div>
                </div>

                <input type="hidden" id="selectedQuestionsInput" name="question_ids" value="[]">
            </div>

            <div class="flex gap-3 sticky bottom-0 bg-white pt-4 mt-6 border-t">
                <button type="button" onclick="document.getElementById('createQuizModal').classList.add('hidden')" 
                        class="flex-1 py-3 border rounded-xl hover:bg-gray-50">Hủy</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl hover:bg-indigo-700">
                    Tạo đề thi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sửa đề thi -->
<div id="editQuizModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-6 sticky top-0 bg-white pt-0">Sửa đề thi</h3>
        <form id="editQuizForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên đề thi</label>
                <input type="text" id="editTitle" name="title" class="w-full border rounded-xl px-4 py-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea name="description" id="editDescription" class="w-full border rounded-xl px-4 py-3" rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Thời gian làm bài (phút)</label>
                <input type="number" id="editDuration" name="duration" class="w-full border rounded-xl px-4 py-3" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Điểm pass (0-10)</label>
                <input type="number" id="editPassScore" name="pass_score" step="0.5" class="w-full border rounded-xl px-4 py-3" required>
            </div>
            <div class="flex gap-3 sticky bottom-0 bg-white pt-4 mt-6 border-t">
                <button type="button" onclick="document.getElementById('editQuizModal').classList.add('hidden')" 
                        class="flex-1 py-3 border rounded-xl hover:bg-gray-50">Hủy</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl hover:bg-indigo-700">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let questionsData = {};
let selectedQuestions = {};

async function loadQuestionsBySubject() {
    const subjectId = document.getElementById('subjectSelect').value;
    
    if (!subjectId) {
        document.getElementById('questionContainer').classList.add('hidden');
        document.getElementById('selectedQuestionsContainer').classList.add('hidden');
        return;
    }

    try {
        const response = await fetch(`/admin/get-questions-by-subject/${subjectId}`);
        const data = await response.json();
        
        questionsData[subjectId] = data.questions;
        
        // Build question list HTML
        let html = '';
        data.questions.forEach(q => {
            const isSelected = selectedQuestions[q.id];
            html += `
                <div class="flex items-start gap-3 p-2 border-b hover:bg-white transition">
                    <input type="checkbox" id="q-${q.id}" value="${q.id}" 
                           class="mt-1 cursor-pointer" 
                           onchange="updateSelectedQuestions()"
                           ${isSelected ? 'checked' : ''}>
                    <label for="q-${q.id}" class="flex-1 cursor-pointer">
                        <div class="font-medium text-sm">${q.question.substring(0, 60)}...</div>
                        <div class="text-xs text-gray-500 mt-1">
                            <span class="inline-block mr-3">Độ khó: <span class="font-semibold capitalize">${q.difficulty}</span></span>
                            <span class="inline-block">Điểm: <span class="font-semibold">${q.marks}</span></span>
                        </div>
                    </label>
                </div>
            `;
        });
        
        document.getElementById('questionList').innerHTML = html;
        document.getElementById('questionContainer').classList.remove('hidden');
        updateSelectedQuestionsDisplay();
    } catch (error) {
        console.error('Error loading questions:', error);
        alert('Lỗi khi tải câu hỏi');
    }
}

function updateSelectedQuestions() {
    selectedQuestions = {};
    
    document.querySelectorAll('input[id^="q-"]:checked').forEach(checkbox => {
        const qId = checkbox.value;
        const q = questionsData[Object.keys(questionsData)[0]]?.find(q => q.id == qId);
        if (q) {
            selectedQuestions[qId] = q;
        }
    });
    
    // Update hidden input with selected question IDs
    document.getElementById('selectedQuestionsInput').value = 
        JSON.stringify(Object.keys(selectedQuestions).map(Number));
    
    updateSelectedQuestionsDisplay();
}

function updateSelectedQuestionsDisplay() {
    const count = Object.keys(selectedQuestions).length;
    document.getElementById('selectedQuestionsCount').textContent = count;
    
    if (count > 0) {
        document.getElementById('selectedQuestionsContainer').classList.remove('hidden');
        
        let html = '';
        Object.values(selectedQuestions).forEach(q => {
            html += `
                <div class="flex items-start justify-between gap-3 p-2 border-b bg-white mb-2 rounded">
                    <div>
                        <div class="font-medium text-sm">${q.question.substring(0, 50)}...</div>
                        <div class="text-xs text-gray-500 mt-1">
                            Độ khó: ${q.difficulty} | Điểm: ${q.marks}
                        </div>
                    </div>
                    <button type="button" onclick="removeQuestion(${q.id})" 
                            class="text-red-600 hover:text-red-800 text-xs">
                        Xóa
                    </button>
                </div>
            `;
        });
        
        document.getElementById('selectedQuestionsList').innerHTML = html;
    } else {
        document.getElementById('selectedQuestionsContainer').classList.add('hidden');
    }
}

function removeQuestion(qId) {
    delete selectedQuestions[qId];
    document.getElementById(`q-${qId}`).checked = false;
    updateSelectedQuestions();
}

function selectAllQuestions() {
    document.querySelectorAll('input[id^="q-"]').forEach(cb => {
        cb.checked = true;
    });
    updateSelectedQuestions();
}

function editQuiz(id, title, duration, passScore) {
    document.getElementById('editTitle').value = title;
    document.getElementById('editDuration').value = duration;
    document.getElementById('editPassScore').value = passScore;
    document.getElementById('editQuizForm').action = '/admin/quizzes/' + id;
    document.getElementById('editQuizModal').classList.remove('hidden');
}
</script>
@endsection