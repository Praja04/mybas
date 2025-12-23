import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-history-vendor-pas-datatables",
            url: API_DATATABLE_ABSENSI_LOGS,
            method: "GET",
            dataColumns: {
                column: [
                    {
                        data: "DT_RowIndex",
                        name: "No",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "nama",
                        name: "nama",
                    },
                    {
                        data: "namacomp",
                        name: "namacomp",
                    },
                    {
                        data: "purpose",
                        name: "purpose",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "no_kartu",
                        name: "no_kartu",
                    },
                    {
                        data: "no_ktp_sim",
                        name: "no_ktp_sim",
                    },
                    {
                        data: "host",
                        name: "host",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "nopol",
                        name: "nopol",
                    },
                    {
                        data: "jenis_tamu",
                        name: "jenis_tamu",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "scan_time",
                        name: "scan_time",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "activity_type",
                        name: "activity_type",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "kartu_dikembalikan",
                        name: "kartu_dikembalikan",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "photo_visitor",
                        name: "photo_visitor",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "img_visitor",
                        name: "img_visitor",
                        orderable: false,
                        searchable: false,
                    },
                ],
            },
            columnDefs: [
                {
                    targets: 1, // Nama Tamu
                    responsivePriority: 1,
                },
                {
                    targets: 9, // Waktu Scan
                    responsivePriority: 2,
                },
                {
                    targets: 10, // Aktivitas
                    responsivePriority: 3,
                },
                {
                    targets: 8, // Jenis Tamu
                    responsivePriority: 4,
                },
            ],

            dataSend: {
                data: {},
            },
            excludeSearchColumns: [
                0, // No
                3, // Tujuan
                6, // Host
                8, // Jenis Tamu
                9, // Waktu Scan
                10, // Aktivitas
                11, // Kartu Dikembalikan
                12, // Foto Diri
                13, // Foto Identitas
            ],
        };
    }

    initialize() {
        return new Promise((resolve, reject) => {
            $(() => {
                const url = new URL(this._datatable.url);
                const filename = url.pathname.split("/").pop();
                console.log("File name:", filename);

                getDatatable(this._datatable)
                    .then(() => {
                        console.log("Datatable initialized successfully");
                        resolve();
                    })
                    .catch((error) => {
                        console.error(
                            "Datatable initialization failed:",
                            error
                        );
                        reject(error);
                    });
            });
        });
    }
}

const contentDatatable = new ContentDatatable();

// Fungsi reload dengan filter
function reloadDataTable(filter) {
    console.log("Destroying existing DataTable...");

    const $table = $(contentDatatable._datatable.className);
    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    console.log("Recreating DataTable with filter:", filter);

    // ✅ Pastikan dataSend dan dataSend.data ada
    if (!contentDatatable._datatable.dataSend) {
        contentDatatable._datatable.dataSend = { data: {} };
    } else if (!contentDatatable._datatable.dataSend.data) {
        contentDatatable._datatable.dataSend.data = {};
    }

    contentDatatable._datatable.dataSend.data.filter = filter;

    contentDatatable
        .initialize()
        .then(() => {
            console.log("Datatable reinitialized with filter:", filter);
        })
        .catch((error) => {
            console.error("Reinitialization failed:", error);
        });
}

// Inisialisasi awal
contentDatatable
    .initialize()
    .then(() => {
        console.log("All datatables initialized successfully");
    })
    .catch((error) => {
        console.error("Initialization failed:", error);
    });

// Handle submit form filter
$("#filter-form").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serializeArray();
    let params = {};

    formData.forEach(({ name, value }) => {
        if (value) {
            params[name] = value;
        }
    });

    // ✅ Perbaikan: sesuaikan dengan nama input 'tanggal_masuk'
    if (params.tanggal_masuk && params.tanggal_masuk.includes(" to ")) {
        const [start, end] = params.tanggal_masuk.split(" to ");
        params.start_date = start;
        params.end_date = end;
        delete params.tanggal_masuk;
    }

    reloadDataTable(params);
});

// Reset filter
$("#filter-form").on("reset", function () {
    setTimeout(() => {
        reloadDataTable({});
    }, 100);
});

// Dropdown filter (jika ada)
document.querySelectorAll(".dropdown-item").forEach((item) => {
    item.addEventListener("click", (event) => {
        event.preventDefault();
        const filterText = event.target.textContent.trim().toLowerCase();

        let filter = {};
        if (filterText === "masuk") {
            filter.activity_type = "in";
        } else if (filterText === "keluar") {
            filter.activity_type = "out";
        }

        document.querySelector(".filter-title").textContent =
            event.target.textContent;
        reloadDataTable(filter);
    });
});
