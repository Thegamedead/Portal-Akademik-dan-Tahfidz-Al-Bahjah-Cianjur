<div 
    x-data="{ 
        toasts: [],
        addToast(toast) {
            toast.id = Date.now() + Math.random().toString(36).substring(2, 9);
            this.toasts.push(toast);
            const duration = (toast.type === 'error' || toast.type === 'warning') ? 7000 : 5000;
            setTimeout(() => {
                this.removeToast(toast.id);
            }, duration);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }" 
    @toast.window="addToast($event.detail)"
    @remove-toast.window="removeToast($event.detail)"
    class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
    role="alert"
    aria-live="polite"
    aria-atomic="true"
>
    <template x-for="toast in toasts" :key="toast.id">
        <x-toast-item />
    </template>
</div>
