import Alpine from 'alpinejs'

window.Alpine = Alpine

/**
 * Lightweight drag-and-drop ordering for the admin grids.
 *
 * Uses the native HTML5 drag events rather than a sortable library: the whole
 * behaviour is thirty lines and it keeps the bundle small.
 */
Alpine.data('sortableGrid', (endpoint) => ({
    dragging: null,
    saving: false,
    message: '',

    start(event, id) {
        this.dragging = id
        event.dataTransfer.effectAllowed = 'move'
    },

    over(event) {
        event.preventDefault()
        event.dataTransfer.dropEffect = 'move'
    },

    drop(event, targetId) {
        event.preventDefault()

        if (this.dragging === null || this.dragging === targetId) {
            return
        }

        const items = [...this.$el.querySelectorAll('[data-id]')].map((el) => Number(el.dataset.id))
        const from = items.indexOf(this.dragging)
        const to = items.indexOf(targetId)

        items.splice(to, 0, ...items.splice(from, 1))

        this.persist(items)
        this.dragging = null
    },

    async persist(ids) {
        this.saving = true

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ ids }),
            })

            const payload = await response.json()
            this.message = payload.message ?? ''
            window.location.reload()
        } finally {
            this.saving = false
        }
    },
}))

/** Modal player for the public reel grid — the iframe only loads on open. */
Alpine.data('reelPlayer', () => ({
    open: false,
    src: '',
    caption: '',

    play(src, caption = '') {
        this.src = src
        this.caption = caption
        this.open = true
        document.body.style.overflow = 'hidden'
    },

    close() {
        this.open = false
        this.src = ''   // unmount the iframe so the video actually stops
        document.body.style.overflow = ''
    },
}))

Alpine.start()
