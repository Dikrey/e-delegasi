<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LoginSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Guest-only authentication routes.
Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::get('blocked', [LoginController::class, 'blocked'])->name('blocked');
});

Route::middleware(['auth', 'session.valid'])->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [\App\Http\Controllers\PageController::class, 'index'])->name('home');

    Route::resource('user', \App\Http\Controllers\UserController::class)
        ->except(['show', 'edit', 'create'])
        ->middleware(['role:admin']);

    Route::get('profile', [\App\Http\Controllers\PageController::class, 'profile'])
        ->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\PageController::class, 'profileUpdate'])
        ->name('profile.update');
    Route::put('profile/deactivate', [\App\Http\Controllers\PageController::class, 'deactivate'])
        ->name('profile.deactivate')
        ->middleware(['role:staff']);

    // Active login sessions / devices.
    Route::get('devices', [LoginSessionController::class, 'index'])->name('devices.index');
    Route::get('devices/data', [LoginSessionController::class, 'data'])->name('devices.data');
    Route::post('devices/{loginSession}/logout', [LoginSessionController::class, 'logout'])
        ->name('devices.logout');
    Route::post('devices/logout-others', [LoginSessionController::class, 'logoutOthers'])
        ->name('devices.logout_others');

    Route::get('settings', [\App\Http\Controllers\PageController::class, 'settings'])
        ->name('settings.show')
        ->middleware(['role:admin']);
    Route::put('settings', [\App\Http\Controllers\PageController::class, 'settingsUpdate'])
        ->name('settings.update')
        ->middleware(['role:admin']);

    Route::delete('attachment', [\App\Http\Controllers\PageController::class, 'removeAttachment'])
        ->name('attachment.destroy');

    Route::prefix('transaction')->as('transaction.')->group(function () {
        Route::resource('incoming', \App\Http\Controllers\IncomingLetterController::class);
        Route::get('incoming/{incoming}/verify', [\App\Http\Controllers\IncomingLetterController::class, 'verify'])
            ->name('incoming.verify')
            ->middleware(['role:admin,sekretaris']);
        Route::post('incoming/{incoming}/verify', [\App\Http\Controllers\IncomingLetterController::class, 'verified'])
            ->name('incoming.verified')
            ->middleware(['role:admin,sekretaris']);
        Route::resource('outgoing', \App\Http\Controllers\OutgoingLetterController::class);
        Route::get('{letter}/disposition/{disposition}/print', [\App\Http\Controllers\DispositionController::class, 'print'])
            ->name('disposition.print');
        Route::get('{letter}/disposition/{disposition}/verify', [\App\Http\Controllers\DispositionController::class, 'verify'])
            ->name('disposition.verify')
            ->middleware(['role:admin,sekretaris']);
        Route::post('{letter}/disposition/{disposition}/verified', [\App\Http\Controllers\DispositionController::class, 'verified'])
            ->name('disposition.verified')
            ->middleware(['role:admin,sekretaris']);
        Route::resource('{letter}/disposition', \App\Http\Controllers\DispositionController::class)->except(['show']);
        Route::post('outgoing/{letter}/finalize', [\App\Http\Controllers\OutgoingLetterController::class, 'finalize'])
            ->name('outgoing.finalize');
    });

    Route::prefix('agenda')->as('agenda.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\IncomingLetterController::class, 'agenda'])->name('incoming');
        Route::get('incoming/print', [\App\Http\Controllers\IncomingLetterController::class, 'print'])->name('incoming.print');
        Route::get('outgoing', [\App\Http\Controllers\OutgoingLetterController::class, 'agenda'])->name('outgoing');
        Route::get('outgoing/print', [\App\Http\Controllers\OutgoingLetterController::class, 'print'])->name('outgoing.print');
    });

    Route::prefix('gallery')->as('gallery.')->group(function () {
        Route::get('incoming', [\App\Http\Controllers\LetterGalleryController::class, 'incoming'])->name('incoming');
        Route::get('outgoing', [\App\Http\Controllers\LetterGalleryController::class, 'outgoing'])->name('outgoing');
    });

    Route::prefix('archive')->as('archive.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('index');
        Route::get('export', [\App\Http\Controllers\ArchiveController::class, 'export'])->name('export');
    });

    Route::prefix('reference')->as('reference.')->middleware(['role:admin'])->group(function () {
        Route::resource('classification', \App\Http\Controllers\ClassificationController::class)->except(['show', 'create', 'edit']);
        Route::resource('status', \App\Http\Controllers\LetterStatusController::class)->except(['show', 'create', 'edit']);
        Route::resource('department', \App\Http\Controllers\DepartmentController::class)->except(['show', 'create', 'edit']);
        Route::resource('task-category', \App\Http\Controllers\TaskCategoryController::class)
            ->except(['show', 'create', 'edit'])
            ->parameters(['task-category' => 'taskCategory']);
    });

    // ══════════════ E-DELEGASI ══════════════

    Route::prefix('delegation')->as('delegation.')->middleware(['role:admin,sekretaris'])->group(function () {
        Route::get('monitoring', [\App\Http\Controllers\DelegationController::class, 'monitoring'])->name('monitoring');
        Route::post('{delegation}/send', [\App\Http\Controllers\DelegationController::class, 'send'])->name('send');
        Route::post('{delegation}/re-delegate', [\App\Http\Controllers\DelegationController::class, 'reDelegate'])->name('redelegate');
        Route::get('create', [\App\Http\Controllers\DelegationController::class, 'create'])->name('create');
        Route::get('/', [\App\Http\Controllers\DelegationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\DelegationController::class, 'store'])->name('store');
        Route::get('{delegation}/edit', [\App\Http\Controllers\DelegationController::class, 'edit'])->name('edit');
        Route::put('{delegation}', [\App\Http\Controllers\DelegationController::class, 'update'])->name('update');
        Route::delete('{delegation}', [\App\Http\Controllers\DelegationController::class, 'destroy'])->name('destroy');
        Route::get('{delegation}', [\App\Http\Controllers\DelegationController::class, 'show'])->name('show');
    });

    Route::prefix('task')->as('task.')->middleware(['auth'])->group(function () {
        Route::get('kanban', [\App\Http\Controllers\TaskController::class, 'kanban'])->name('kanban');
        Route::post('{task}/status', [\App\Http\Controllers\TaskController::class, 'kanbanUpdate'])->name('kanban.update');
        Route::post('{task}/accept', [\App\Http\Controllers\TaskController::class, 'accept'])->name('accept');
        Route::post('{task}/reject', [\App\Http\Controllers\TaskController::class, 'reject'])->name('reject');
        Route::post('{task}/progress', [\App\Http\Controllers\TaskController::class, 'updateProgress'])->name('progress');
        Route::post('{task}/verify', [\App\Http\Controllers\TaskController::class, 'verify'])->name('verify')
            ->middleware(['role:sekretaris']);
        Route::get('{task}/download', [\App\Http\Controllers\TaskController::class, 'download'])->name('download');
        Route::get('{task}/update/{update}/download', [\App\Http\Controllers\TaskController::class, 'downloadUpdate'])->name('update-download');
        Route::get('{task}', [\App\Http\Controllers\TaskController::class, 'show'])->name('show');
        Route::get('/', [\App\Http\Controllers\TaskController::class, 'index'])->name('index');
    });

    Route::prefix('agenda-pimpinan')->as('agenda-pimpinan.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AgendaController::class, 'index'])->name('index');
        Route::get('list', [\App\Http\Controllers\AgendaController::class, 'list'])->name('list');
        Route::get('data', [\App\Http\Controllers\AgendaController::class, 'data'])->name('data');

        Route::middleware(['role:admin,sekretaris'])->group(function () {
            Route::get('create', [\App\Http\Controllers\AgendaController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\AgendaController::class, 'store'])->name('store');
            Route::post('{agenda}/status', [\App\Http\Controllers\AgendaController::class, 'changeStatus'])->name('status');
            Route::post('{agenda}/reschedule', [\App\Http\Controllers\AgendaController::class, 'reschedule'])->name('reschedule');
            Route::get('{agenda}/edit', [\App\Http\Controllers\AgendaController::class, 'edit'])->name('edit');
            Route::put('{agenda}', [\App\Http\Controllers\AgendaController::class, 'update'])->name('update');
            Route::delete('{agenda}', [\App\Http\Controllers\AgendaController::class, 'destroy'])->name('destroy');
        });

        Route::get('{agenda}', [\App\Http\Controllers\AgendaController::class, 'show'])->name('show');
    });

    Route::prefix('notifications')->as('notification.')->group(function () {
        Route::post('read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read_all');
        Route::get('poll', [\App\Http\Controllers\NotificationController::class, 'poll'])->name('poll');
        Route::match(['get', 'post'], '{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
        Route::delete('{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('activity-logs')->as('activity-log.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('index');
    });

    Route::prefix('reports')->as('report.')->middleware(['role:admin,sekretaris'])->group(function () {
        Route::get('delegation', [\App\Http\Controllers\ReportController::class, 'delegation'])->name('delegation');
        Route::get('delegation/export', [\App\Http\Controllers\ReportController::class, 'exportDelegation'])->name('delegation.export');
        Route::get('task', [\App\Http\Controllers\ReportController::class, 'task'])->name('task');
        Route::get('task/export', [\App\Http\Controllers\ReportController::class, 'exportTask'])->name('task.export');
        Route::get('agenda', [\App\Http\Controllers\ReportController::class, 'agenda'])->name('agenda');
        Route::get('agenda/export', [\App\Http\Controllers\ReportController::class, 'exportAgenda'])->name('agenda.export');
    });

});

// Halaman yang sudah dihapus / tidak tersedia.
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});