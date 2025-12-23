import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-security-gate-datatables",
            url: API_DATATABLE_ABSENSI_LOG_GATE,
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
                        data: "security_nama",
                        name: "security_nama",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "security_nik",
                        name: "security_nik",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "visitor_nama",
                        name: "visitor_nama",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "visitor_company",
                        name: "visitor_company",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "visitor_purpose",
                        name: "visitor_purpose",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "no_ktp_sim",
                        name: "no_ktp_sim",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "nopol",
                        name: "nopol",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "waktu",
                        name: "waktu",
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
                        data: "photo_visitor",
                        name: "photo_visitor",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "photo_gate",
                        name: "photo_gate",
                        orderable: false,
                        searchable: false,
                    },
                    // {
                    //     data: "action",
                    //     name: "Aksi",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                ],
            },
            columnDefs: [
                {
                    targets: 1, // Nama Security
                    orderable: true,
                    searchable: true,
                    responsivePriority: 1,
                },
                {
                    targets: 8, // Waktu Akses
                    responsivePriority: 2,
                },
                {
                    targets: 9, // Aktivitas
                    responsivePriority: 3,
                },
            ],
            dataSend: {
                data: {},
            },
            excludeSearchColumns: [0, 5, 8, 9, 10, 11],
        };
    }

    initialize() {
        return new Promise((resolve, reject) => {
            $(() => {
                const url = new URL(this._datatable.url);
                const filename = url.pathname.split("/").pop();
                console.log("Initializing Datatable from:", filename);

                getDatatable(this._datatable)
                    .then(() => {
                        console.log("✅ Datatable initialized successfully");
                        resolve();
                    })
                    .catch((error) => {
                        console.error(
                            "❌ Datatable initialization failed:",
                            error
                        );
                        reject(error);
                    });
            });
        });
    }
}

// Instansiasi
const contentDatatable = new ContentDatatable();

// Fungsi reload dengan filter
function reloadDataTable(filter) {
    console.log("🔄 Destroying existing DataTable...");

    const $table = $(contentDatatable._datatable.className);
    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    // Pastikan struktur dataSend dan dataSend.data ada
    if (!contentDatatable._datatable.dataSend) {
        contentDatatable._datatable.dataSend = { data: {} };
    } else if (!contentDatatable._datatable.dataSend.data) {
        contentDatatable._datatable.dataSend.data = {};
    }

    // Simpan filter
    contentDatatable._datatable.dataSend.data.filter = filter;

    console.log("🚀 Reinitializing DataTable with filter:", filter);

    contentDatatable
        .initialize()
        .then(() => {
            console.log("✅ DataTable reloaded with new filter");
        })
        .catch((error) => {
            console.error("❌ Reload failed:", error);
        });
}

// Inisialisasi pertama kali
contentDatatable
    .initialize()
    .then(() => {
        console.log("🎉 All datatables initialized successfully");
    })
    .catch((error) => {
        console.error("💀 Initialization failed:", error);
    });

// Handle submit form filter
$("#filter-form").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serializeArray();
    const params = {};

    formData.forEach(({ name, value }) => {
        if (value) {
            params[name] = value;
        }
    });

    // Proses rentang tanggal: "start_date" dan "end_date"
    if (params.tanggal_masuk && params.tanggal_masuk.includes(" to ")) {
        const [start, end] = params.tanggal_masuk.split(" to ");
        params.start_date = start.trim();
        params.end_date = end.trim();
        delete params.tanggal_masuk;
    }

    reloadDataTable(params);
});

// Reset form
$("#filter-form").on("reset", function () {
    setTimeout(() => {
        reloadDataTable({});
    }, 100);
});

// Dropdown filter (contoh: Masuk / Keluar)
document.querySelectorAll(".dropdown-item").forEach((item) => {
    item.addEventListener("click", (event) => {
        event.preventDefault();
        const text = event.target.textContent.trim().toLowerCase();

        let filter = {};
        if (text === "masuk") {
            filter.activity_type = "in";
        } else if (text === "keluar") {
            filter.activity_type = "out";
        }

        document.querySelector(".filter-title").textContent =
            event.target.textContent;
        reloadDataTable(filter);
    });
});
