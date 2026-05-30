<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\ExamSessions\ExamSessionResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Dashboard';

    protected string $view = 'filament.admin.pages.dashboard';

    protected Width | string | null $maxContentWidth = Width::Full;

    public function getHeading(): string | Htmlable | null
    {
        return null;
    }

    protected function getViewData(): array
    {
        $enrollmentsCount = CourseEnrollment::count();
        $completedEnrollmentsCount = CourseEnrollment::completed()->count();
        $completionRate = $enrollmentsCount > 0
            ? (int) round(($completedEnrollmentsCount / $enrollmentsCount) * 100)
            : 0;

        return [
            'greeting' => $this->getGreeting(),
            'adminName' => Auth::user()?->name ?? 'Admin',
            'todayLabel' => now()->format('d M Y'),
            'stats' => [
                [
                    'label' => 'Total Pengguna',
                    'value' => Number::format(User::count()),
                    'description' => 'Seluruh akun yang terdaftar',
                ],
                [
                    'label' => 'Total Siswa',
                    'value' => Number::format(User::role('student')->count()),
                    'description' => 'Pengguna dengan role student',
                ],
                [
                    'label' => 'Total Pengajar',
                    'value' => Number::format(User::role('teacher')->count()),
                    'description' => 'Pengguna dengan role teacher',
                ],
                [
                    'label' => 'Kursus Aktif',
                    'value' => Number::format(Course::where('status', 'published')->count()),
                    'description' => 'Kursus berstatus published',
                ],
                [
                    'label' => 'Enrollments',
                    'value' => Number::format($enrollmentsCount),
                    'description' => "Completion rate {$completionRate}%",
                ],
            ],
            'quickLinks' => [
                [
                    'label' => 'Kelola Pengguna',
                    'description' => 'Atur admin, teacher, dan student dari satu tempat.',
                    'url' => UserResource::getUrl(),
                ],
                [
                    'label' => 'Kelola Kursus',
                    'description' => 'Lihat course aktif, draft, dan performa konten.',
                    'url' => CourseResource::getUrl(),
                ],
                [
                    'label' => 'Kategori',
                    'description' => 'Rapikan struktur kategori agar navigasi belajar lebih jelas.',
                    'url' => CategoryResource::getUrl(),
                ],
                [
                    'label' => 'Exam Sessions',
                    'description' => 'Pantau sesi ujian dan progres evaluasi pembelajaran.',
                    'url' => ExamSessionResource::getUrl(),
                ],
            ],
            'recentEnrollments' => CourseEnrollment::query()
                ->with(['user:id,name', 'course:id,title,status'])
                ->latest('enrolled_at')
                ->limit(5)
                ->get(),
        ];
    }

    protected function getGreeting(): string
    {
        return match (true) {
            now()->hour < 11 => 'Selamat pagi',
            now()->hour < 15 => 'Selamat siang',
            now()->hour < 19 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}
