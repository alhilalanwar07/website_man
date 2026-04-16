<?php

namespace App\Livewire\Admin\PpdbV2;

use Livewire\Component;

class Broadcast extends Component
{
    public $targetAudience = 'all'; // all, belum_daftar_ulang, dll
    public $channel = 'whatsapp'; // whatsapp, email
    public $messageText = '';

    public function sendBroadcast()
    {
        $this->validate([
            'targetAudience' => 'required',
            'channel' => 'required',
            'messageText' => 'required|min:5',
        ]);

        // Logic pengiriman di sini (API Gateway / Mail)
        // Dummy simulasi:
        
        session()->flash('message', 'Broadcast telah dimasukkan ke dalam antrean (Queue) untuk segera dikirimkan via ' . strtoupper($this->channel) . ' ke partisipan terpilih.');
        $this->reset('messageText');
    }

    public function render()
    {
        return view('livewire.admin.ppdb-v2.broadcast')->layout('components.layouts.admin', ['title' => 'Broadcast & Pengumuman']);
    }
}
