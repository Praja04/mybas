import { getDatatable } from "../../shared/services/datatable.services.js";

export class ContentDatatableOut {
    constructor() {
        this._datatable = {
            className: ".kendaraan-out-datatables",
            url: API_DATATABLE_KENDARAAN_OUT,
            method: "GET",
            language: {
                emptyTable: "Semua kendaraan sudah dicek keluar",
                zeroRecords: "Data kendaraan tidak ditemukan",
                loadingRecords: "Memuat data...",
                processing: "Loading...",
            },
            dataColumns: {
                column: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "nomor_polisi",
                        name: "v.nopol",
                        orderable: false,
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
            },
            columnDefs: [
                {
                    targets: 1, // Nomor Polisi
                    responsivePriority: 1,
                },
                {
                    targets: -1, // Action
                    responsivePriority: 2,
                },
            ],
            dataSend: {},
        };
    }

    initialize() {
        return new Promise((resolve, reject) => {
            $(() => {
                // Extracting filename from URL
                const url = new URL(this._datatable.url);
                const filename = url.pathname.split("/").pop();

                getDatatable(this._datatable)
                    .then((dt) => {
                        console.log("datatable out content initialized");
                        window.cekKendaraanOutTable = dt;
                        resolve();
                    })
                    .catch((error) => {
                        console.error(
                            "datatable out content initialization failed:",
                            error
                        );
                        reject(error);
                    });
            });
        });
    }
}

const contentDatatableOut = new ContentDatatableOut();

// Initial datatable load
contentDatatableOut
    .initialize()
    .then(() => {
        console.log("All datatables initialized successfully");
    })
    .catch((error) => {
        console.error("Initialization failed:", error);
    });
