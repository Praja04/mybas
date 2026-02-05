/**
 * ⚠️ PENTING – KONFIGURASI FOTO PENGECEKAN KENDARAAN
 *
 * - Key object (misalnya: "TRUK MUAT GULA CAIR", "FRUKTOSA", dst)
 *   adalah IDENTIFIER BISNIS dan dipakai sebagai:
 *   1. Referensi pemetaan jenis truk → daftar foto wajib
 *   2. Sumber pembentukan key foto (turunan) di frontend & backend
 *   3. Acuan data existing (database, IndexedDB draft, histori cek)
 *
 * - Value array (label foto) dipakai untuk:
 *   1. 
 *   2. Render UI label foto
 *   3. Membentuk baseKey foto (di-sanitize & di-lowercase)
 *   4. Menentukan slot foto WAJIB / OPSIONAL
 *
 * 🚫 JANGAN:
 * - Mengubah nama KEY object (string paling kiri)
 * - Menghapus KEY object yang sudah ada
 *
 * Karena:
 * - Key sudah tersimpan di:
 *   • IndexedDB draft (offline)
 *   • Pengecekan kendaraan IN (jika sudah dicek)
 * - Perubahan akan menyebabkan:
 *   • Render UI pengambilan foto tidak muncul saat OUT karena IN pakai key lama, dan keynya sudah diganti/dihapus
 *
 * ✅ BOLEH:
 * - Menambahkan LABEL BARU di dalam array
 * - Menambahkan KEY BARU (jenis truk baru) di level paling bawah
 *
 * CATATAN:
 * - "Temuan Barang Mencurigakan (Tidak Wajib)"
 *   diperlakukan sebagai OPSIONAL oleh sistem
 * - Semua label lain otomatis dianggap WAJIB
 */
window.fotoConfig = {
    "TRUK MUAT GULA CAIR": [ // key
        "Kondisi Segel 1 (Atas)",
        "Kondisi Segel 2 (Belakang)",
        "Kondisi Kabin Dashboard",
        "Kondisi Kabin Laci Dashboard",
        "Kondisi Kabin Bawah Jok",
        "Kondisi Kabin Belakang Jok",
        "Kondisi Atap Kabin",
        "Kolong Mobil Truck Sebelah Kanan",
        "Kolong Mobil Truck Sebelah Kiri",
        "Kolong Mobil Truck Belakang",
        "Kotak Toolkit dan Dongkrak",
        // "Kolong Belakang",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "FRUKTOSA": [
        "Kondisi Segel 1 (Belakang)",
        "Kondisi Kabin Dashboard",
        "Kondisi Kabin Laci Dashboard",
        "Kondisi Kabin Bawah Jok",
        "Kondisi Kabin Belakang Jok",
        "Kondisi Atap Kabin",
        "Kolong Mobil Truck Sebelah Kanan",
        "Kolong Mobil Truck Sebelah Kiri",
        "Kolong Mobil Truck Belakang",
        "Kotak Toolkit dan Dongkrak",
        // "Kolong Belakang",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "LAINNYA (LIQUID)": [
        "Kondisi Segel 1 (Atas)",
        "Kondisi Segel 2 (Belakang)",
        "Kondisi Kabin Dashboard",
        "Kondisi Kabin Laci Dashboard",
        "Kondisi Kabin Bawah Jok",
        "Kondisi Kabin Belakang Jok",
        "Kondisi Atap Kabin",
        "Kolong Mobil Truck Sebelah Kanan",
        "Kolong Mobil Truck Sebelah Kiri",
        "Kolong Mobil Truck Belakang",
        "Kotak Toolkit dan Dongkrak",
        // "Kolong Belakang",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "TRUK BONGKAR MATERIAL": [
        "Kabin Depan Mobil",
        "Kondisi Dalam Bak Truck",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "TRUK RAW MATERIAL": [
        "Kabin Depan Mobil",
        "Kondisi Dalam Bak Truck",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "TRUK BONGKAR FINISH GOOD": [
        "Kabin Depan Mobil",
        "Kondisi Bak Truck",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "TRUK MUAT FINISH GOOD": [
        "Kabin Depan Mobil",
        "Kondisi Bak Truck",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "MOBIL SPAREPART": [
        "Surat Jalan atau Pembelian Barang",
        "Kondisi Dalam Kendaraan atau Bak Kendaraan",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "MOBIL VENDOR": [
        "Surat Jalan atau Pembelian Barang",
        "Kondisi Dalam Kendaraan atau Bak Kendaraan",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "MOBIL PENGANGKUT SAMPAH": [
        "Surat Jalan atau Pembelian Barang",
        "Kondisi Dalam Kendaraan atau Bak Kendaraan",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
    "LAINNYA (NONLIQUID)": [
        "Surat Jalan atau Pembelian Barang",
        "Kondisi Dalam Kendaraan atau Bak Kendaraan",
        "Temuan Barang Mencurigakan (Tidak Wajib)",
    ],
};
