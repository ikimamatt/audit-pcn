<?php

namespace Tests\Feature;

use App\Models\MasterData\MasterUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExitMeetingWebTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_exit_meeting_create_page_renders_successfully()
    {
        $user = MasterUser::whereHas('akses', function($q) {
            $q->whereIn('nama_akses', ['SUPER ADMIN', 'AUDITOR', 'KSPI', 'ASMAN SPI']);
        })->first();

        if (!$user) {
            $user = MasterUser::first();
        }

        $response = $this->actingAs($user)->get('/audit/exit-meeting/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Exit Meeting');
        $response->assertSee('Perencanaan Audit');
    }
}
