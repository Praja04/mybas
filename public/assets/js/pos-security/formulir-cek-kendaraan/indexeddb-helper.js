(function () {
    const DB_NAME = "cek_kendaraan_db";
    const DB_VERSION = 1;
    const STORE = "cek_kendaraan_drafts";

    function openDB() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);

            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(STORE)) {
                    db.createObjectStore(STORE, {
                        keyPath: "sessionId",
                    });
                }
            };

            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async function saveDraft(data) {
        const db = await openDB();
        const tx = db.transaction(STORE, "readwrite");
        const store = tx.objectStore(STORE);

        data.lastUpdated = new Date();
        data.status = "draft";

        store.put(data);
        return tx.complete;
    }

    async function getDraft(sessionId) {
        const db = await openDB();
        const tx = db.transaction(STORE, "readonly");
        const store = tx.objectStore(STORE);

        return new Promise((resolve) => {
            const req = store.get(sessionId);
            req.onsuccess = () => resolve(req.result || null);
        });
    }

    async function deleteDraft(sessionId) {
        const db = await openDB();
        const tx = db.transaction(STORE, "readwrite");
        tx.objectStore(STORE).delete(sessionId);
    }

    window.IDBDraft = {
        saveDraft,
        getDraft,
        deleteDraft,
    };
})();
