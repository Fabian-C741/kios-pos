/**
 * GESTOR OFFLINE PROFESIONAL - KIOSCO POS
 * Maneja la detección de conexión y cola de ventas.
 */
class OfflineManager {
    constructor() {
        this.statusIndicator = null;
        this.syncQueue = JSON.parse(localStorage.getItem('sync_queue')) || [];
        this.init();
    }

    init() {
        window.addEventListener('online', () => this.handleStatusChange(true));
        window.addEventListener('offline', () => this.handleStatusChange(false));
        
        // Registro del Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('PWA Lista para funcionar offline'))
                .catch(err => console.warn('Error en Service Worker:', err));
        }

        // Cargar catálogo para uso offline
        if (navigator.onLine) {
            this.updateProductCache();
            this.syncPendingSales();
        }
    }

    async updateProductCache() {
        try {
            const response = await fetch('/offline-products');
            const products = await response.json();
            localStorage.setItem('offline_products', JSON.stringify(products));
            console.log('Catálogo offline actualizado:', products.length, 'productos');
        } catch (err) {
            console.warn('No se pudo actualizar el catálogo offline:', err);
        }
    }

    getProductByCode(code) {
        const products = JSON.parse(localStorage.getItem('offline_products')) || [];
        return products.find(p => p.codigo_barras === code);
    }

    handleStatusChange(isOnline) {
        this.updateUI(isOnline);
        if (isOnline) {
            this.syncPendingSales();
        }
    }

    updateUI(isOnline) {
        const indicator = document.getElementById('offline-indicator');
        if (!indicator) return;

        if (isOnline) {
            indicator.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3';
            indicator.innerHTML = '<i class="bi bi-wifi"></i> Online';
        } else {
            indicator.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3';
            indicator.innerHTML = '<i class="bi bi-wifi-off"></i> Offline (Ventas Locales)';
        }
    }

    /**
     * Guarda una venta en la cola local si no hay internet.
     */
    queueSale(saleData) {
        this.syncQueue.push({
            data: saleData,
            timestamp: new Date().getTime()
        });
        localStorage.setItem('sync_queue', JSON.stringify(this.syncQueue));
        
        alert("¡SIN CONEXIÓN! La venta se guardó en este dispositivo y se subirá sola al volver la luz/internet.");
    }

    /**
     * Envía las ventas pendientes al servidor.
     */
    async syncPendingSales() {
        if (this.syncQueue.length === 0) return;

        console.log('Sincronizando ventas pendientes...');
        const pending = [...this.syncQueue];
        
        for (const item of pending) {
            try {
                const response = await fetch('/ventas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(item.data)
                });

                if (response.ok) {
                    this.syncQueue = this.syncQueue.filter(i => i.timestamp !== item.timestamp);
                    localStorage.setItem('sync_queue', JSON.stringify(this.syncQueue));
                }
            } catch (err) {
                console.error('Error al sincronizar venta:', err);
                break; // Detener si falla la conexión de nuevo
            }
        }
    }
}

const offlineManager = new OfflineManager();
window.offlineManager = offlineManager;
