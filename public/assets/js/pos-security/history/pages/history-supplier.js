import { getDatatable } from "../../../shared/services/datatable.services.js";

const datatableConfig = {
    className: ".ga-history-supplier-pas-datatables",
    url: API_DATATABLE_HISTORY_SUPPLIER,
    method: "GET",
    dataColumns: {
        column: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "namavisitor", name: "namavisitor", orderable: true, searchable: true },
            { data: "keterangan", name: "keterangan", orderable: false, searchable: false },
            { data: "namacomp", name: "namacomp", orderable: true, searchable: true },
            { data: "nopol", name: "nopol", orderable: true, searchable: true },
            { data: "no_kartu", name: "no_kartu", orderable: true, searchable: true },
            { data: "waktu_masuk", name: "waktu_masuk", orderable: false, searchable: false },
            { data: "waktu_keluar", name: "waktu_keluar", orderable: false, searchable: false },
            { data: "action", name: "action", orderable: false, searchable: false },
        ],
    },
    columnDefs: [
        { targets: 1, responsivePriority: 1 },
        { targets: 3, responsivePriority: 2 },
        { targets: -3, responsivePriority: 3 },
        { targets: -2, responsivePriority: 4 },
        { targets: -1, responsivePriority: 5 },
    ],
    dataSend: {},
    excludeSearchColumns: [0, 2, -3, -2, -1],
};

// Expose ke global agar bisa diakses dari inline script
window._supplierDatatableConfig = datatableConfig;

$(() => {
    getDatatable(datatableConfig)
        .then((table) => {
            window._supplierTable = table;
            console.log("Supplier datatable initialized");
        })
        .catch((err) => console.error("Datatable init failed:", err));
});
