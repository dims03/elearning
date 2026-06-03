<?php

use App\Exports\CourseReportExport;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/admin/access', function (Request $request) {
    $request->session()->put('admin_login_shortcut_unlocked', true);

    return redirect()->route('filament.admin.auth.login');
})->name('admin.shortcut');

Route::get('/teacher/export/course/{courseId}', function ($courseId) {
    $course = Course::where('id', $courseId)
        ->where('teacher_id', Auth::id())
        ->firstOrFail();

    $filename = 'laporan-' . \Str::slug($course->title) . '-' . now()->format('Ymd') . '.xlsx';

    return Excel::download(new CourseReportExport($courseId), $filename);
})->middleware(['auth'])->name('teacher.export.course');
