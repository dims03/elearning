<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingAccessTest extends TestCase
{
    public function test_landing_page_renders_student_and_teacher_portals(): void
    {
        $response = $this->get(route('landing'));

        $response
            ->assertOk()
            ->assertSee('Masuk Student Panel')
            ->assertSee('Masuk Teacher Panel')
            ->assertDontSee('Masuk Admin Panel');
    }

    public function test_admin_login_requires_shortcut_access(): void
    {
        $response = $this->get(route('filament.admin.auth.login'));

        $response
            ->assertRedirect(route('landing'))
            ->assertSessionHas('adminShortcutNotice');
    }

    public function test_admin_shortcut_unlocks_login_page(): void
    {
        $this->get(route('admin.shortcut'))
            ->assertRedirect(route('filament.admin.auth.login'));

        $this->get(route('filament.admin.auth.login'))
            ->assertOk();
    }
}
