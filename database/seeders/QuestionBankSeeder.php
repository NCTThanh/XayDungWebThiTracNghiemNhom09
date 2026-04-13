<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // Define all 12 subjects with 20 questions each
        $subjects_data = [
            1 => ['name' => 'Toán Học Cơ Bản', 'slug' => 'toan-hoc-co-ban'],
            2 => ['name' => 'Tiếng Anh Sơ Cấp', 'slug' => 'tieng-anh-so-cap'],
            3 => ['name' => 'Lập Trình Web', 'slug' => 'lap-trinh-web'],
            4 => ['name' => 'Lịch Sử Đại Cương', 'slug' => 'lich-su-dai-cuong'],
            5 => ['name' => 'Địa Lý Thế Giới', 'slug' => 'dia-ly-the-gioi'],
            6 => ['name' => 'Hóa Học Cơ Bản', 'slug' => 'hoa-hoc-co-ban'],
            7 => ['name' => 'Vật Lý Cơ Bản', 'slug' => 'vat-ly-co-ban'],
            8 => ['name' => 'Sinh Học Cơ Bản', 'slug' => 'sinh-hoc-co-ban'],
            9 => ['name' => 'Xã Hội Học', 'slug' => 'xa-hoi-hoc'],
            10 => ['name' => 'Kinh Tế Chính Trị', 'slug' => 'kinh-te-chinh-tri'],
            11 => ['name' => 'Triết Học', 'slug' => 'triet-hoc'],
            12 => ['name' => 'Thể Dục Thể Thao', 'slug' => 'the-duc-the-thao'],
        ];

        // Insert subjects if they don't exist
        foreach ($subjects_data as $id => $subject) {
            DB::table('subjects')->updateOrInsert(
                ['id' => $id],
                array_merge($subject, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // All 240 questions (20 per subject)
        $questions = $this->getQuestions($now);
        
        // Insert questions in chunks
        foreach (array_chunk($questions, 100) as $chunk) {
            DB::table('questions')->insert($chunk);
        }

        // All 960 options (4 per question)
        $options = $this->getOptions($now);
        
        // Insert options in chunks
        foreach (array_chunk($options, 200) as $chunk) {
            DB::table('options')->insert($chunk);
        }
    }

    private function getQuestions($now)
    {
        $questions = [];
        $qid = 100; // Start from ID 100 to avoid conflicts

        // SUBJECT 1: TOÁN HỌC CƠ BẢN
        $math_qs = [
            '2+3=?', '10-4=?', '3*4=?', '12÷3=?', '7+8=?', '20-5=?', '6*2=?',
            '15*12=?', '(5+3)*2=?', '25% + 50%=?', 'x+8=15, x=?', '√100=?', '2³=?', '3/4 của 20=?',
            '(2³+3²)*2=?', '5!=?', 'log₁₀(100)=?', '(a+b)³=?', 'sin(45°)=?', 'Giải: 2x+5=13'
        ];
        $difficulties = ['easy', 'easy', 'easy', 'easy', 'easy', 'easy', 'easy', 
                        'medium', 'medium', 'medium', 'medium', 'medium', 'medium', 'medium',
                        'hard', 'hard', 'hard', 'hard', 'hard', 'hard'];
        $marks = [1, 1, 1, 1, 1, 1, 1, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 2, 2, 2, 2, 2, 2];
        
        foreach ($math_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 1,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $difficulties[$idx],
                'marks' => $marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 2: TIẾNG ANH SƠ CẤP
        $english_qs = [
            '"Hello" có nghĩa là?', '"Thank you" dịch là?', '"Water" trong tiếng Việt là?', '"Good morning" = ?', '"Please" có nghĩa là?', '"Sorry" = ?', '"Goodbye" = ?',
            '"Are you going to school?" - Thì nào?', '"I have studied English for 5 years" - Thì?', '"He does not like pizza" - Phủ định đúng?', '"Where is the nearest hospital?" - "Where" là từ loại?', 'Anh ngữ có bao nhiêu thì cơ bản?', '"Beautiful" là tính từ hay danh từ?', '"Knowledge" - Danh từ đếm được hay không?',
            '"If I knew the answer, I would tell you" - Loại?', '"Despite his poverty, he was happy" - "despite"?', '"The book was written by Shakespeare" - Thể?', '"Not until did he apologize" - Cấu trúc gì?', 'Tính chất của tiếng Anh như thế nào?', 'Câu hỏi đuôi (tag question) dùng để?'
        ];
        $eng_difficulties = ['easy', 'easy', 'easy', 'easy', 'easy', 'easy', 'easy',
                             'medium', 'medium', 'medium', 'medium', 'medium', 'medium', 'medium',
                             'hard', 'hard', 'hard', 'hard', 'hard', 'hard'];
        $eng_marks = [1, 1, 1, 1, 1, 1, 1, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 2, 2, 2, 2, 2, 2];
        
        foreach ($english_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 2,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $eng_difficulties[$idx],
                'marks' => $eng_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 3: LẬP TRÌNH WEB
        $prog_qs = [
            'HTML là gì?', 'CSS dùng để làm gì?', 'JavaScript là gì?', 'PHP là ngôn ngữ gì?', 'MySQL là gì?', '<h1> tag trong HTML dùng để?', 'Thẻ <img> dùng để?',
            'CSS Flexbox là gì?', 'JavaScript "async/await" là gì?', 'REST API là gì?', 'Responsive design là gì?', 'Bootstrap framework dùng để?', 'MVC là gì trong lập trình?', 'OOP là gì?',
            'Microservices architecture?', 'Docker container là gì?', 'JWT authentication là gì?', 'WebSocket dùng để?', 'CI/CD pipeline là gì?', 'GraphQL vs REST API khác gì?'
        ];
        $prog_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $prog_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($prog_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 3,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $prog_difficulties[$idx],
                'marks' => $prog_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 4: LỊCH SỬ ĐẠI CƯƠNG
        $hist_qs = [
            'Chiến tranh Thế giới thứ 1 kết thúc năm?', 'Cách mạng Tháng Mười xảy ra ở đâu?', 'Napoleon là vị tướng của đất nước nào?', 'Thời Phục Hưng bắt đầu ở?', 'Ai là lãnh đạo Cách mạng Pháp?', 'Chiến tranh Thế giới thứ 2 kết thúc năm?', 'Cộng hòa Xô Viết thành lập năm?',
            'Đế chế La Mã sụp đổ vào thế kỷ?', 'Phong kiến Châu Âu bắt đầu từ?', 'Nền văn minh Ai Cập cổ đại phát triển ở?', 'Cách mạng Công nghiệp bắt đầu ở?', 'Liên Hợp Quốc thành lập năm?', 'Bức tường Berlin xây dựng năm?', 'Đế quốc Inca ở?',
            'Hiệp ước Westphalia ký năm?', 'Nền văn minh Minoan ở?', 'Chiến tranh Lạnh kéo dài bao lâu?', 'Đế chế Ottoman sụp đổ vào?', 'Hiệp ước Brest-Litovsk ký?', 'Cuộc cách mạng Tây Ban Nha?'
        ];
        $hist_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $hist_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($hist_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 4,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $hist_difficulties[$idx],
                'marks' => $hist_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 5: ĐỊA LÝ THẾ GIỚI
        $geo_qs = [
            'Thủ đô Pháp là?', 'Thủ đô Nhật Bản là?', 'Sông Nile ở?', 'Đỉnh Everest ở?', 'Thủ đô Trung Quốc là?', 'Bảo tàng Louvre ở?', 'Vòng tay Thành phố New York?',
            'Sahara là sa mạc ở?', 'Amazon là rừng mưa ở?', 'Biển Caspian ở?', 'Đảo Iceland ở?', 'Rặng Andes ở?', 'Vịnh Hudson ở?', 'Sông Yangtze là sông lớn?',
            'Rặng Ural tách biệt Châu Âu với?', 'Eo biển Malacca nối?', 'Vinh Vịnh Mexico ở?', 'Biển Chết ở?', 'Maldives nằm ở?', 'Do Đại Tây Dương là?'
        ];
        $geo_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $geo_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($geo_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 5,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $geo_difficulties[$idx],
                'marks' => $geo_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 6: HÓA HỌC CƠ BẢN
        $chem_qs = [
            'O₂ có bao nhiêu nguyên tử oxy?', 'H₂O là gì?', 'NaCl là gì?', 'Electron là?', 'Proton có điện tích?', 'pH = 7 là?', 'CO₂ là gì?',
            'Hóa trị của Carbon?', 'Số Avogadro là?', 'Liên kết Cộng hóa trị là?', 'Phản ứng Exothermic là?', 'Công thức C₆H₁₂O₆ là gì?', 'Xúc tác là?', 'Quá trình oxy hóa là?',
            'Định luật Hess là?', 'Phổ hấp thụ là?', 'Phương trình Nernst là?', 'Cơ chế phản ứng SN2 là?', 'Điện tích Formal là?', 'Quỹ đạo Molecular là?'
        ];
        $chem_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $chem_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($chem_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 6,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $chem_difficulties[$idx],
                'marks' => $chem_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 7: VẬT LÝ CƠ BẢN
        $phys_qs = [
            'Tốc độ ánh sáng khoảng?', 'Công thức F=ma gọi là?', 'Trọng lượng là?', 'Gia tốc rơi tự do?', 'Năng lượng là gì?', 'Công suất được đo?', 'sin(0°)=?',
            'Moment lực là?', 'Động lượng công thức?', 'Lực đàn hồi Hooke là?', 'Lực Coriolis là?', 'Năng lượng điện từ là?', 'Chuyển động tròn đều có?', 'Điện trổng là?',
            'Lý thuyết tương đối hẹp Einstein?', 'Hằng số Planck là?', 'Entropy là gì?', 'Lượng tử là gì?', 'Nguyên lý bất định Heisenberg?', 'Vector sóng là?'
        ];
        $phys_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $phys_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($phys_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 7,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $phys_difficulties[$idx],
                'marks' => $phys_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 8: SINH HỌC CƠ BẢN
        $bio_qs = [
            'DNA là gì?', 'Nhân tố di truyền là?', 'Sợi ADN có bao nhiêu mạch?', 'RNA là gì?', 'Mitochondria của tế bào là?', 'Biển xanh ở tế bào?', 'Enzyme là gì?',
            'Meiosis là quá trình?', 'Mitosis là?', 'Protein được tạo từ?', 'Photosynthesis là?', 'Respiration là?', 'Allele là?', 'Genotype là?',
            'CRISPR là công nghệ gì?', 'Apoptosis là?', 'Telomere là?', 'Histone là?', 'Transposable element là?', 'Gene expression là?'
        ];
        $bio_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $bio_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($bio_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 8,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $bio_difficulties[$idx],
                'marks' => $bio_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 9: XÃ HỘI HỌC
        $soc_qs = [
            'Xã hội học là gì?', 'Văn hóa là gì?', 'Chuẩn mực xã hội là?', 'Vai trò xã hội là?', 'Status là?', 'Tập quán là?', 'Nhóm xã hội là?',
            'Stratification là?', 'Socialization là?', 'Deviance là?', 'Control theory là?', 'Mores là?', 'Institution là?', 'Subculture là?',
            'Structuralism là gì?', 'Functionalism là gì?', 'Conflict theory là?', 'Symbolic interactionism là?', 'Habitus của Bourdieu?', 'Globalization là?'
        ];
        $soc_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $soc_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($soc_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 9,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $soc_difficulties[$idx],
                'marks' => $soc_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 10: KINH TẾ CHÍNH TRỊ
        $econ_qs = [
            'Kinh tế học là gì?', 'GDP là gì?', 'Cung cầu là gì?', 'Lạm phát là?', 'Lãi suất là?', 'Thất nghiệp là?', 'Thị trường tự do là?',
            'Elasticity là?', 'Opportunity cost là?', 'Comparative advantage là?', 'Monopoly là?', 'Oligopoly là?', 'Externality là?', 'Public goods là?',
            'Keynesian economics là?', 'Laissez-faire là?', 'Stagflation là?', 'Phillips curve là?', 'Gini coefficient là?', 'Quantitative easing là?'
        ];
        $econ_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $econ_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($econ_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 10,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $econ_difficulties[$idx],
                'marks' => $econ_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 11: TRIẾT HỌC
        $phil_qs = [
            'Triết học là gì?', 'Logic là gì?', 'Siêu hình là gì?', 'Nhân bản học là?', 'Tinh thần là?', 'Chân lý là?', 'Đạo đức là?',
            'Idealism là?', 'Materialism là?', 'Empiricism là?', 'Rationalism là?', 'Stoicism là?', 'Epicureanism là?', 'Existentialism là?',
            'Platonism là gì?', 'Kant\'s categorical imperative?', 'Hegelian dialectic là?', 'Nietzschean philosophy?', 'Phenomenology là?', 'Structuralist philosophy?'
        ];
        $phil_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $phil_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($phil_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 11,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $phil_difficulties[$idx],
                'marks' => $phil_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        // SUBJECT 12: THỂ DỤC THỂ THAO
        $pe_qs = [
            'Bóng đá có bao nhiêu cầu thủ?', 'Bóng chuyền sân có bao nhiêu cầu thủ?', 'Chiều cao lưới bóng chuyền?', 'Olympic được tổ chức?', 'Bóng rổ có bao nhiêu cầu thủ?', 'Tennis court có bao nhiêu cầu thủ?', 'Marathon chạy bao xa?',
            'VO2 max là gì?', 'Aerobic exercise là?', 'Anaerobic exercise là?', 'BMI là gì?', 'Flexibility của cơ thể?', 'Endurance training là?', 'Strength training là?',
            'Lactate threshold là?', 'Periodization training là?', 'Interval training là?', 'Proprioception là?', 'Sport biomechanics là?', 'Doping test là?'
        ];
        $pe_difficulties = array_merge(array_fill(0, 7, 'easy'), array_fill(0, 7, 'medium'), array_fill(0, 6, 'hard'));
        $pe_marks = array_merge(array_fill(0, 7, 1), array_fill(0, 7, 1.5), array_fill(0, 6, 2));
        
        foreach ($pe_qs as $idx => $q) {
            $questions[] = [
                'id' => $qid,
                'quiz_id' => null,
                'subject_id' => 12,
                'question' => $q,
                'type' => 'single',
                'difficulty' => $pe_difficulties[$idx],
                'marks' => $pe_marks[$idx],
                'created_at' => $now,
                'updated_at' => $now
            ];
            $qid++;
        }

        return $questions;
    }

    private function getOptions($now)
    {
        $options = [];
        $oid = 1000;

        // Generate 4 options per question (240 questions × 4 = 960 options)
        for ($qid = 100; $qid < 340; $qid++) {
            // Random correct option position (0-3)
            $correct_pos = rand(0, 3);
            
            for ($i = 0; $i < 4; $i++) {
                $options[] = [
                    'id' => $oid,
                    'question_id' => $qid,
                    'option_text' => ($i + 1) . ($i == $correct_pos ? ' (Đáp án đúng)' : ' (Lựa chọn sai)'),
                    'is_correct' => $i == $correct_pos ? 1 : 0,
                    'order' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $oid++;
            }
        }

        return $options;
    }
}
