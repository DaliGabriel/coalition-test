function taskSorter() {
    return {
        init() {
            Sortable.create(this.$el, {
                handle: '.drag-handle',
                animation: 150,
                forceFallback: true,
                fallbackClass: 'shadow-lg opacity-90',
                ghostClass: 'bg-indigo-50',
                onEnd: () => {
                    const items = this.$el.querySelectorAll('li[data-id]');

                    items.forEach((item, index) => {
                        item.querySelector('.priority-badge').textContent = index + 1;
                    });

                    const orderedIds = Array.from(items).map(item => parseInt(item.dataset.id));

                    fetch(this.$el.dataset.reorderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ tasks: orderedIds }),
                    }).then(response => {
                        if (!response.ok) {
                            alert('Could not save the new order. Please refresh the page.');
                        }
                    });
                },
            });
        },
    };
}
