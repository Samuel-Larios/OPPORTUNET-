<?php

namespace App\Livewire\Panel;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsCenter extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $filter = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function openNotification(string $notificationId)
    {
        $notification = $this->notificationQuery()->findOrFail($notificationId);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $actionUrl = $notification->data['action_url'] ?? null;

        if (is_string($actionUrl) && $actionUrl !== '') {
            return redirect()->to($actionUrl);
        }

        return null;
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = $this->notificationQuery()->findOrFail($notificationId);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function deleteNotification(string $notificationId): void
    {
        $this->notificationQuery()->findOrFail($notificationId)->delete();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $notifications = $this->notificationQuery()
            ->when($this->filter === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->when($search !== '', fn ($query) => $query->where('data', 'like', '%' . $search . '%'))
            ->latest()
            ->paginate(12);

        return view('livewire.panel.notifications-center', [
            'notifications' => $notifications,
            'unreadCount' => auth()->user()?->unreadNotifications()->count() ?? 0,
        ]);
    }

    protected function notificationQuery()
    {
        return auth()->user()?->notifications() ?? DatabaseNotification::query()->whereRaw('1 = 0');
    }
}
