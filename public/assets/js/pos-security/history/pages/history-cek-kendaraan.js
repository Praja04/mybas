import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-history-cek-kendaraan-datatables",
            url: API_DATATABLE_HISTORY_KENDARAAN,
            method: "GET",
            dataColumns: {
                column: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                    },
                    {
                        data: "nomor_polisi",
                        name: "nomor_polisi",
                    },
                    {
                        data: "nama_supir",
                        name: "nama_supir",
                    },
                    {
                        data: "company",
                        name: "company",
                    },
                    {
                        data: "nama_petugas",
                        name: "nama_petugas",
                    },
                    {
                        data: "waktu_pemeriksaan",
                        name: "waktu_pemeriksaan",
                    },
                    {
                        data: "jenis",
                        name: "jenis",
                    },
                    {
                        data: "action",
                        name: "action",
                    },
                ],
            },
            dataSend: {},
            excludeSearchColumns: [0],
        };
    }

    initialize() {
        return new Promise((resolve, reject) => {
            $(() => {
                // Extracting filename from URL
                const url = new URL(this._datatable.url);
                const filename = url.pathname.split("/").pop();
                console.log("File name:", filename);

                getDatatable(this._datatable)
                    .then(() => {
                        console.log("datatable content initialized");
                        resolve();
                    })
                    .catch((error) => {
                        console.error(
                            "datatable content  initialization failed:",
                            error
                        );
                        reject(error);
                    });
            });
        });
    }
}

const contentDatatable = new ContentDatatable();

// Initial datatable load
contentDatatable
    .initialize()
    .then(() => {
        console.log("All datatables initialized successfully");
    })
    .catch((error) => {
        console.error("Initialization failed:", error);
    });
