<?php

namespace Tests\Feature;

use App\Models\LogChatbot;
use App\Models\Produk;
use App\Models\User;
use App\Services\ChatbotStokService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    private function jawab(string $pesan, ?User $user = null): string
    {
        return app(ChatbotStokService::class)->jawab($pesan, $user);
    }

    public function test_satu_hasil_stok_aman(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 20, 'harga_jual' => 12500]);

        $this->assertSame('Kopi Bubuk tersedia, stok 20 unit. Harga jual Rp 12.500.', $this->jawab('stok kopi bubuk ada?'));
    }

    public function test_satu_hasil_stok_menipis(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 5, 'harga_jual' => 12500]);

        $this->assertSame('Kopi Bubuk tersisa 5 unit (menipis). Harga jual Rp 12.500.', $this->jawab('stok kopi bubuk'));
    }

    public function test_satu_hasil_stok_habis(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 0]);

        $this->assertSame('Kopi Bubuk sedang habis.', $this->jawab('kopi bubuk'));
    }

    public function test_kata_harga_dan_huruf_besar_memberi_jawaban_yang_sama(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 20, 'harga_jual' => 12500]);

        $this->assertSame($this->jawab('stok kopi bubuk ada?'), $this->jawab('HARGA Kopi Bubuk berapa'));
    }

    public function test_ambang_stok_menipis_mengikuti_config(): void
    {
        config(['toko.stok_menipis' => 10]);
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 8, 'harga_jual' => 1000]);

        $this->assertStringContainsString('(menipis)', $this->jawab('kopi bubuk'));
    }

    public function test_banyak_hasil_menampilkan_daftar(): void
    {
        Produk::factory()->create(['nama_produk' => 'Teh Hijau', 'stok' => 3, 'harga_jual' => 5000]);
        Produk::factory()->create(['nama_produk' => 'Teh Melati', 'stok' => 0, 'harga_jual' => 6000]);
        Produk::factory()->create(['nama_produk' => 'Teh Tarik', 'stok' => 12, 'harga_jual' => 7000]);
        Produk::factory()->create(['nama_produk' => 'Gula Pasir', 'stok' => 12, 'harga_jual' => 7000]);

        $jawaban = $this->jawab('stok teh');

        $this->assertSame(
            "Ada beberapa produk yang cocok:\n- Teh Hijau: stok 3, Rp 5.000\n- Teh Melati: stok 0, Rp 6.000\n- Teh Tarik: stok 12, Rp 7.000",
            $jawaban
        );
        $this->assertStringNotContainsString('Gula Pasir', $jawaban);
    }

    public function test_hasil_dibatasi_lima_produk(): void
    {
        foreach (range(1, 7) as $i) {
            Produk::factory()->create(['nama_produk' => "Teh Rasa $i"]);
        }

        $this->assertSame(5, substr_count($this->jawab('teh'), "\n- "));
    }

    public function test_kecocokan_persis_didahulukan(): void
    {
        Produk::factory()->create(['nama_produk' => 'Aqua Botol', 'stok' => 9, 'harga_jual' => 4000]);
        Produk::factory()->create(['nama_produk' => 'Aqua', 'stok' => 9, 'harga_jual' => 3000]);

        $this->assertSame(
            "Ada beberapa produk yang cocok:\n- Aqua: stok 9, Rp 3.000\n- Aqua Botol: stok 9, Rp 4.000",
            $this->jawab('aqua')
        );
    }

    public function test_produk_tidak_ditemukan(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk']);

        $this->assertSame(
            'Maaf, produk "xyzabc" tidak ditemukan. Coba cek ejaan atau gunakan nama yang lebih singkat.',
            $this->jawab('xyzabc')
        );
    }

    public function test_sapaan(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk']);

        foreach (['halo', 'Hai!', 'selamat pagi'] as $sapaan) {
            $jawaban = $this->jawab($sapaan);
            $this->assertStringStartsWith('Halo!', $jawaban);
            $this->assertStringContainsString('contoh: "stok Kopi Bubuk"', $jawaban);
        }
    }

    public function test_pesan_kosong_atau_hanya_kata_bantu_memberi_bantuan(): void
    {
        $bantuan = 'Ketik nama barang untuk cek stok, contoh: "stok nama barang".';

        $this->assertSame($bantuan, $this->jawab('stok'));
        $this->assertSame($bantuan, $this->jawab('berapa stok barang ya kak?'));
        $this->assertSame($bantuan, $this->jawab(''));
        $this->assertSame($bantuan, $this->jawab('   ?!  '));
    }

    public function test_bantuan_memakai_produk_pertama_sebagai_contoh(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk']);
        Produk::factory()->create(['nama_produk' => 'Aqua']);

        $this->assertSame('Ketik nama barang untuk cek stok, contoh: "stok Kopi Bubuk".', $this->jawab('stok'));
    }

    public function test_endpoint_menolak_pesan_kosong(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson(route('chatbot.pesan'), ['pesan' => ''])
            ->assertStatus(422)->assertJsonValidationErrors('pesan');
    }

    public function test_setiap_pertanyaan_dicatat_ke_log(): void
    {
        $user = User::factory()->create();
        $produk = Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 20]);

        $this->actingAs($user)->postJson(route('chatbot.pesan'), ['pesan' => 'stok kopi bubuk'])->assertOk();
        $this->actingAs($user)->postJson(route('chatbot.pesan'), ['pesan' => 'xyzabc'])->assertOk();

        $this->assertSame(2, LogChatbot::count());
        $this->assertDatabaseHas('log_chatbot', [
            'user_id' => $user->id, 'produk_id' => $produk->id, 'pertanyaan' => 'stok kopi bubuk',
        ]);
        $this->assertDatabaseHas('log_chatbot', ['user_id' => $user->id, 'produk_id' => null, 'pertanyaan' => 'xyzabc']);
    }

    public function test_respons_chatbot_tidak_memuat_harga_beli_atau_keuntungan_untuk_pemilik(): void
    {
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 20, 'harga_beli' => 8765, 'harga_jual' => 12500]);
        Produk::factory()->create(['nama_produk' => 'Kopi Susu', 'stok' => 2, 'harga_beli' => 8765, 'harga_jual' => 12500]);
        $pemilik = User::factory()->pemilik()->create();

        foreach (['stok kopi bubuk', 'harga kopi', 'kopi', 'halo', 'stok'] as $pesan) {
            $teks = $this->actingAs($pemilik)->postJson(route('chatbot.pesan'), ['pesan' => $pesan])
                ->assertOk()->getContent();

            $this->assertStringNotContainsString('8765', $teks);
            $this->assertStringNotContainsString('8.765', $teks);
            $this->assertStringNotContainsString('3735', $teks);
            $this->assertStringNotContainsString('3.735', $teks);
            $this->assertStringNotContainsStringIgnoringCase('keuntungan', $teks);
            $this->assertStringNotContainsStringIgnoringCase('harga_beli', $teks);
        }
    }

    public function test_log_chatbot_hanya_untuk_pemilik(): void
    {
        $this->actingAs(User::factory()->create())->get(route('chatbot.log'))->assertForbidden();
        $this->actingAs(User::factory()->pemilik()->create())->get(route('chatbot.log'))->assertOk();
    }

    public function test_chatbot_wajib_login_bila_tidak_publik(): void
    {
        config(['toko.chatbot_public' => false]);

        $this->get('/chatbot')->assertRedirect(route('login'));
        $this->postJson('/chatbot/pesan', ['pesan' => 'stok kopi'])->assertUnauthorized();
        $this->assertSame(0, LogChatbot::count());
    }

    public function test_chatbot_bisa_dibuka_tamu_bila_publik(): void
    {
        config(['toko.chatbot_public' => true]);
        Produk::factory()->create(['nama_produk' => 'Kopi Bubuk', 'stok' => 20, 'harga_jual' => 12500]);

        $this->get('/chatbot')->assertOk();
        $this->postJson('/chatbot/pesan', ['pesan' => 'stok kopi bubuk'])
            ->assertOk()
            ->assertJson(['jawaban' => 'Kopi Bubuk tersedia, stok 20 unit. Harga jual Rp 12.500.']);

        $this->assertDatabaseHas('log_chatbot', ['user_id' => null, 'pertanyaan' => 'stok kopi bubuk']);
    }

    public function test_chatbot_publik_tidak_membuka_rute_lain_untuk_tamu(): void
    {
        config(['toko.chatbot_public' => true]);

        $this->get(route('produk.index'))->assertRedirect(route('login'));
        $this->get(route('penjualan.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('chatbot.log'))->assertRedirect(route('login'));
    }

    public function test_pengguna_login_bisa_memakai_chatbot_saat_tidak_publik(): void
    {
        config(['toko.chatbot_public' => false]);

        $this->actingAs(User::factory()->create())->get('/chatbot')->assertOk();
    }
}
