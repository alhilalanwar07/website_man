<?php

namespace App\Livewire\Frontend;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Hubungi Admin PPDB - MAN 2 Kolaka')]
class PpdbContactAdmin extends Component
{
    public string $adminEmail = '';
    public string $adminWhatsapp = '';
    public ?string $mailtoLink = null;
    public ?string $whatsappLink = null;

    public function mount(): void
    {
        $this->adminEmail = trim((string) config('services.ppdb_contact.admin_email', ''));
        $this->adminWhatsapp = $this->normalizeWhatsapp((string) config('services.ppdb_contact.admin_whatsapp', ''));

        $subject = rawurlencode('Permintaan Bantuan/Ubah Data PPDB');
        $body = rawurlencode("Halo Admin PPDB,\n\nSaya membutuhkan bantuan terkait data pendaftaran PPDB.\nNomor pendaftaran: \nNama lengkap: \nKendala/perubahan data: \n\nTerima kasih.");

        if ($this->adminEmail !== '') {
            $this->mailtoLink = 'mailto:' . $this->adminEmail . '?subject=' . $subject . '&body=' . $body;
        }

        if ($this->adminWhatsapp !== '') {
            $text = rawurlencode("Halo Admin PPDB, saya butuh bantuan terkait pendaftaran.");
            $this->whatsappLink = 'https://wa.me/' . $this->adminWhatsapp . '?text=' . $text;
        }
    }

    public function render()
    {
        return view('livewire.frontend.ppdb-contact-admin');
    }

    protected function normalizeWhatsapp(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone));

        if (! is_string($digits) || $digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '';
    }
}
