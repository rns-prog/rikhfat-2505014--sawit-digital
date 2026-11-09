<?php

namespace Tests\Feature;

use App\Models\Kebun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KebunManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_sent_to_login_before_accessing_kebun(): void
    {
        $this->get(route('kebun.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_can_register_and_log_out(): void
    {
        $this->post(route('register'), [
            'name' => 'Petani Baru',
            'email' => 'petani@example.test',
            'password' => 'sandi-aman-123',
            'password_confirmation' => 'sandi-aman-123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_a_user_can_create_and_update_their_own_kebun(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('kebun.store'), [
            'nama' => 'Kebun Barat',
            'lokasi' => 'Desa Makmur',
            'luas_ha' => 2.75,
            'user_id' => User::factory()->create()->id,
        ])->assertRedirect(route('kebun.index'));

        $kebun = Kebun::query()->where('nama', 'Kebun Barat')->firstOrFail();
        $this->assertSame($user->id, $kebun->user_id);

        $this->put(route('kebun.update', $kebun), [
            'nama' => 'Kebun Barat Baru',
            'lokasi' => 'Desa Sejahtera',
            'luas_ha' => 3.25,
        ])->assertRedirect(route('kebun.index'));

        $this->assertDatabaseHas('kebuns', [
            'id' => $kebun->id,
            'user_id' => $user->id,
            'nama' => 'Kebun Barat Baru',
            'lokasi' => 'Desa Sejahtera',
            'luas_ha' => 3.25,
        ]);
    }

    public function test_a_user_only_sees_and_can_change_their_own_kebun(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownKebun = $owner->kebuns()->create([
            'nama' => 'Kebun Saya',
            'lokasi' => 'Lokasi Saya',
            'luas_ha' => 1.50,
        ]);
        $otherKebun = $otherUser->kebuns()->create([
            'nama' => 'Kebun Orang Lain',
            'lokasi' => 'Lokasi Lain',
            'luas_ha' => 4.00,
        ]);

        $this->actingAs($owner)
            ->get(route('kebun.index'))
            ->assertOk()
            ->assertSee('Kebun Saya')
            ->assertDontSee('Kebun Orang Lain');

        $this->get(route('kebun.edit', $otherKebun))->assertNotFound();
        $this->put(route('kebun.update', $otherKebun), [
            'nama' => 'Diubah tanpa izin',
            'lokasi' => 'Lokasi',
            'luas_ha' => 4,
        ])->assertNotFound();
        $this->delete(route('kebun.destroy', $otherKebun))->assertNotFound();

        $this->assertDatabaseHas('kebuns', [
            'id' => $ownKebun->id,
            'nama' => 'Kebun Saya',
        ]);
        $this->assertDatabaseHas('kebuns', [
            'id' => $otherKebun->id,
            'nama' => 'Kebun Orang Lain',
        ]);
    }

    public function test_a_user_can_delete_their_kebun_and_the_form_asks_for_confirmation(): void
    {
        $user = User::factory()->create();
        $kebun = $user->kebuns()->create([
            'nama' => 'Kebun Hapus',
            'lokasi' => 'Desa Contoh',
            'luas_ha' => 1.25,
        ]);

        $this->actingAs($user)
            ->get(route('kebun.index'))
            ->assertSee('confirm(')
            ->assertSee('Hapus kebun ini beserta semua catatan terkait?');

        $this->delete(route('kebun.destroy', $kebun))
            ->assertRedirect(route('kebun.index'))
            ->assertSessionHas('success', 'Kebun berhasil dihapus.');

        $this->assertDatabaseMissing('kebuns', ['id' => $kebun->id]);
    }

    public function test_kebun_fields_are_validated(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('kebun.create'))
            ->post(route('kebun.store'), [
                'nama' => '',
                'lokasi' => '',
                'luas_ha' => 0,
            ])
            ->assertRedirect(route('kebun.create'))
            ->assertSessionHasErrors(['nama', 'lokasi', 'luas_ha']);
    }
}
