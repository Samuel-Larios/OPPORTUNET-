<?php

namespace App\Livewire\Panel;

use App\Models\SecurityIncident;
use App\Models\SecurityIpBlock;
use App\Support\SecurityMonitor;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SecurityCenter extends Component
{
    use WithPagination;

    public string $manualIpAddress = '';

    public string $manualReason = 'manual_admin_block';

    public function blockIp(string $ipAddress): void
    {
        SecurityMonitor::manualBlock($ipAddress, 'manual_admin_block', auth()->user());

        session()->flash('panel_success', __('admin.security.flash.blocked'));
    }

    public function unblockIp(int $blockId): void
    {
        $block = SecurityIpBlock::query()->findOrFail($blockId);

        SecurityMonitor::unblock($block);

        session()->flash('panel_success', __('admin.security.flash.unblocked'));
    }

    public function blockManualIp(): void
    {
        $this->validate([
            'manualIpAddress' => ['required', 'ip'],
            'manualReason' => ['required', 'string', 'max:255'],
        ]);

        SecurityMonitor::manualBlock($this->manualIpAddress, $this->manualReason, auth()->user());

        $this->reset('manualIpAddress');

        session()->flash('panel_success', __('admin.security.flash.blocked'));
    }

    public function render(): View
    {
        $activeBlocksQuery = SecurityIpBlock::query()
            ->where(function ($query) {
                $query
                    ->where('is_manual', true)
                    ->orWhereNull('blocked_until')
                    ->orWhere('blocked_until', '>', now());
            });

        return view('livewire.panel.security-center', [
            'stats' => [
                'active_blocks' => (clone $activeBlocksQuery)->count(),
                'incidents_24h' => SecurityIncident::query()->where('created_at', '>=', now()->subDay())->count(),
                'critical_24h' => SecurityIncident::query()->where('severity', 'critical')->where('created_at', '>=', now()->subDay())->count(),
            ],
            'activeBlocks' => $activeBlocksQuery->latest('last_triggered_at')->get(),
            'recentIncidents' => SecurityIncident::query()->latest('created_at')->paginate(20),
        ]);
    }
}
