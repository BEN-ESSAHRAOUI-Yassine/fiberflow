import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

Alpine.data('projectMap', (projectId, center, zoom) => ({
    map: null,
    geoJsonLayer: null,
    selectedLayer: '',
    searchQuery: '',
    featureCount: 0,
    loading: false,

    init() {
        const container = this.$el.querySelector('[x-ref="mapContainer"]');

        if (container._leaflet_id) {
            container._leaflet_map?.remove();
        }

        this.map = L.map(container).setView(center, zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(this.map);

        this.loadFeatures();
    },

    async loadFeatures() {
        this.loading = true;

        try {
            const params = new URLSearchParams();
            if (this.selectedLayer) params.set('layer', this.selectedLayer);
            if (this.searchQuery) params.set('search', this.searchQuery);

            const response = await fetch(`/projects/${projectId}/network?${params}`);
            const json = await response.json();

            this.displayFeatures(json.data?.features ?? []);
        } catch (e) {
            console.error('Failed to load network data:', e);
        } finally {
            this.loading = false;
        }
    },

    displayFeatures(features) {
        if (this.geoJsonLayer) {
            this.map.removeLayer(this.geoJsonLayer);
        }

        if (features.length === 0) {
            this.geoJsonLayer = null;
            this.featureCount = 0;
            return;
        }

        const collection = { type: 'FeatureCollection', features };

        this.geoJsonLayer = L.geoJSON(collection, {
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, {
                    radius: 6,
                    fillColor: this.getColor(feature),
                    color: '#fff',
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8,
                });
            },
            style: (feature) => {
                if (feature.geometry?.type === 'LineString' || feature.geometry?.type === 'MultiLineString') {
                    return {
                        color: this.getColor(feature),
                        weight: 3,
                        opacity: 0.7,
                    };
                }
                if (feature.geometry?.type === 'Polygon' || feature.geometry?.type === 'MultiPolygon') {
                    return {
                        fillColor: this.getColor(feature),
                        color: '#333',
                        weight: 1,
                        fillOpacity: 0.3,
                    };
                }
                return {};
            },
            onEachFeature: (feature, layer) => {
                if (feature.properties) {
                    const rows = Object.entries(feature.properties)
                        .map(([k, v]) => `<tr><td class="font-medium pr-2">${k}</td><td>${v ?? ''}</td></tr>`)
                        .join('');
                    layer.bindPopup(`<table class="text-xs">${rows}</table>`);
                }
            },
        }).addTo(this.map);

        this.featureCount = features.length;

        if (features.length > 0) {
            this.map.fitBounds(this.geoJsonLayer.getBounds(), { padding: [20, 20] });
        }
    },

    filterFeatures() {
        this.loadFeatures();
    },

    getColor(feature) {
        const code = feature.properties?.cb_code || feature.properties?.nd_code || feature.properties?.zn_code || '';
        const type = feature.properties?.nd_type || feature.properties?.pt_typephy || feature.properties?.ch_typ || '';

        const colorMap = {
            'transport': '#6366f1',
            'distribution': '#10b981',
            'NRO': '#f59e0b',
            'SRO': '#ef4444',
            'PBO': '#8b5cf6',
        };

        for (const [key, color] of Object.entries(colorMap)) {
            if (code.toLowerCase().includes(key) || type.toLowerCase().includes(key)) {
                return color;
            }
        }

        return '#6366f1';
    },
}));

Alpine.data('paginate', (initialItems, perPage = 10) => ({
    allItems: initialItems,
    perPage,
    currentPage: 1,

    get totalPages() {
        return Math.ceil(this.allItems.length / this.perPage);
    },

    get paginatedItems() {
        const start = (this.currentPage - 1) * this.perPage;
        return this.allItems.slice(start, start + this.perPage);
    },

    get totalItems() {
        return this.allItems.length;
    },

    prev() {
        if (this.currentPage > 1) this.currentPage--;
    },

    next() {
        if (this.currentPage < this.totalPages) this.currentPage++;
    },

    goTo(page) {
        this.currentPage = Math.max(1, Math.min(page, this.totalPages));
    },
}));

Alpine.data('auditChat', (auditId, projectId) => ({
    conversationId: null,
    messages: [],
    message: '',
    loading: false,

    async init() {
        await this.fetchConversation();
    },

    async fetchConversation() {
        try {
            const response = await fetch(`/projects/${projectId}/audits/${auditId}/chat`);
            const data = await response.json();

            this.conversationId = data.conversation_id;
            this.messages = data.messages ?? [];
        } catch (e) {
            console.error('Failed to load conversation:', e);
        }
    },

    async sendMessage() {
        if (!this.message.trim() || this.loading) return;

        const text = this.message;
        this.message = '';
        this.loading = true;

        this.messages.push({ role: 'user', content: text });

        try {
            const response = await fetch(`/projects/${projectId}/audits/${auditId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: this.conversationId,
                }),
            });

            const data = await response.json();

            this.conversationId = data.conversation_id;
            this.messages.push({ role: 'assistant', content: data.reply });

            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) container.scrollTop = container.scrollHeight;
            });
        } catch (e) {
            console.error('Chat failed:', e);
            this.messages.push({ role: 'assistant', content: 'Désolé, une erreur est survenue.' });
        } finally {
            this.loading = false;
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();
