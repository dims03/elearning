<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExamSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseReportExport implements WithMultipleSheets
{
    public function __construct(
        private int $courseId
    ) {}

    public function sheets(): array
    {
        $course = Course::with([
            'teacher',
            'enrollments.user',
            'exams.sessions' => fn($q) => $q->where('status', 'graded'),
        ])->findOrFail($this->courseId);

        return [
            new CourseEnrollmentSheet($course),
            new CourseExamResultSheet($course),
        ];
    }
}

// ── Sheet 1: Data Enrollment & Progress ──────────────────────────────────────

class CourseEnrollmentSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    public function __construct(private Course $course) {}

    public function title(): string
    {
        return 'Progress Siswa';
    }

    public function collection()
    {
        return CourseEnrollment::with('user')
            ->where('course_id', $this->course->id)
            ->orderBy('enrolled_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Email',
            'Tanggal Enroll',
            'Progress (%)',
            'Status',
            'Tanggal Selesai',
            'Nilai Ujian',
            'Status Ujian',
        ];
    }

    public function map($enrollment): array
    {
        static $no = 0;
        $no++;

        $bestSession = ExamSession::whereHas(
            'exam',
            fn($q) =>
            $q->where('course_id', $this->course->id)
        )
            ->where('user_id', $enrollment->user_id)
            ->where('status', 'graded')
            ->orderByDesc('score')
            ->first();

        return [
            $no,
            $enrollment->user->name,
            $enrollment->user->email,
            $enrollment->enrolled_at?->format('d/m/Y'),
            $enrollment->progress_percent . '%',
            match ($enrollment->status) {
                'active'    => 'Aktif',
                'completed' => 'Selesai',
                'dropped'   => 'Keluar',
                default     => $enrollment->status,
            },
            $enrollment->completed_at?->format('d/m/Y') ?? '—',
            $bestSession ? $bestSession->score . '%' : 'Belum Ujian',
            $bestSession ? ($bestSession->is_passed ? 'LULUS' : 'TIDAK LULUS') : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Judul kursus di atas tabel
                $event->sheet->insertNewRowBefore(1, 3);
                $event->sheet->setCellValue('A1', 'LAPORAN PROGRESS SISWA');
                $event->sheet->setCellValue('A2', 'Kursus: ' . $this->course->title);
                $event->sheet->setCellValue('A3', 'Guru: ' . ($this->course->teacher->name ?? '—'));

                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2:A3')->getFont()->setSize(11);
                $event->sheet->getStyle('1:3')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Freeze header row
                $event->sheet->freezePane('A5');

                $highestRow = $event->sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $status = $event->sheet->getCell('I' . $row)->getValue();
                    if ($status === 'LULUS') {
                        $event->sheet->getStyle('A' . $row . ':I' . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFD4EDDA');
                    } elseif ($status === 'TIDAK LULUS') {
                        $event->sheet->getStyle('A' . $row . ':I' . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFF8D7DA');
                    }
                }
            },
        ];
    }
}

// ── Sheet 2: Hasil Ujian ──────────────────────────────────────────────────────

class CourseExamResultSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithEvents
{
    public function __construct(private Course $course) {}

    public function title(): string
    {
        return 'Hasil Ujian';
    }

    public function collection()
    {
        return ExamSession::with(['user', 'exam'])
            ->whereHas('exam', fn($q) => $q->where('course_id', $this->course->id))
            ->where('status', 'graded')
            ->orderBy('exam_id')
            ->orderBy('user_id')
            ->orderBy('attempt_number')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Email',
            'Ujian',
            'Percobaan ke-',
            'Nilai (%)',
            'Status',
            'Mulai',
            'Submit',
        ];
    }

    public function map($session): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $session->user->name,
            $session->user->email,
            $session->exam->title,
            $session->attempt_number,
            $session->score . '%',
            $session->is_passed ? 'LULUS' : 'TIDAK LULUS',
            $session->started_at?->format('d/m/Y H:i'),
            $session->submitted_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->insertNewRowBefore(1, 3);
                $event->sheet->setCellValue('A1', 'LAPORAN HASIL UJIAN');
                $event->sheet->setCellValue('A2', 'Kursus: ' . $this->course->title);
                $event->sheet->setCellValue('A3', 'Guru: ' . ($this->course->teacher->name ?? '—'));

                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2:A3')->getFont()->setSize(11);
                $event->sheet->freezePane('A5');

                // Warnai baris LULUS/TIDAK LULUS
                $highestRow = $event->sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $status = $event->sheet->getCell('G' . $row)->getValue();
                    if ($status === 'LULUS') {
                        $event->sheet->getStyle('A' . $row . ':I' . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFD4EDDA');
                    } elseif ($status === 'TIDAK LULUS') {
                        $event->sheet->getStyle('A' . $row . ':I' . $row)
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFF8D7DA');
                    }
                }
            },
        ];
    }
}
