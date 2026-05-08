<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan')]
class Settings extends Component
{
    // Existing Setting Modal Properties
    public bool $showModal = false;
    public ?int $editId = null;
    public string $key = '';
    public string $value = '';
    public string $type = 'string';

    // System Environment Properties
    public string $appEnv = 'local';
    public bool $appDebug = true;
    public bool $isMaintenanceMode = false;
    public string $maintenanceSecret = 'admin123'; // Default secret

    public function mount()
    {
        $this->appEnv = config('app.env', 'local');
        $this->appDebug = config('app.debug', true);
        $this->isMaintenanceMode = app()->isDownForMaintenance();
    }

    public function updateSystemEnv()
    {
        $this->setEnvValue('APP_ENV', $this->appEnv);
        $this->setEnvValue('APP_DEBUG', $this->appDebug ? 'true' : 'false');
        
        // Clear caches to apply changes
        Artisan::call('optimize:clear');
        
        $this->dispatch('toast', type: 'success', message: 'Environment system berhasil diupdate.');
    }

    public function toggleMaintenance()
    {
        if ($this->isMaintenanceMode) {
            Artisan::call('up');
            $this->isMaintenanceMode = false;
            $this->dispatch('toast', type: 'success', message: 'Website kembali Online (Mode Maintenance dimatikan).');
        } else {
            // Put into maintenance mode with secret
            Artisan::call('down', [
                '--secret' => $this->maintenanceSecret,
            ]);
            $this->isMaintenanceMode = true;
            $this->dispatch('toast', type: 'warning', message: "Mode Maintenance Aktif. Gunakan link /{$this->maintenanceSecret} untuk akses Bypass.");
        }
    }

    private function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $env = file_get_contents($path);
            
            // Check if key exists
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}\n";
            }
            
            file_put_contents($path, $env);
        }
    }

    // Existing methods below
    public function create(): void
    {
        $this->editId = null;
        $this->key = '';
        $this->value = '';
        $this->type = 'string';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $s = Setting::findOrFail($id);
        $this->editId = $s->id;
        $this->key = $s->key;
        $this->value = $s->value ?? '';
        $this->type = $s->type;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable|string',
            'type' => 'required|in:string,boolean,json,image',
        ]);

        Setting::updateOrCreate(
            ['id' => $this->editId],
            ['key' => $this->key, 'value' => $this->value, 'type' => $this->type]
        );

        $this->showModal = false;
        $this->dispatch('toast', type: 'success', message: 'Setting berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        Setting::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Setting dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'settings' => Setting::orderBy('key')->get(),
        ]);
    }
}
