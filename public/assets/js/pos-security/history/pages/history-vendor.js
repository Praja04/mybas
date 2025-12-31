import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-history-vendor-pas-datatables",
            url: API_DATATABLE_HISTORY_VENDOR,
            method: "GET",
            dataColumns: {
                column: [
                    {
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "namacomp",
                        name: "namacomp",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "namavisitor",
                        name: "namavisitor",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "tgl_lahir",
                        name: "tgl_lahir",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "host",
                        name: "host",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "keperluan",
                        name: "keperluan",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "hostdeptid",
                        name: "hostdeptid",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "no_kartu",
                        name: "no_kartu",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "no_ktp_sim",
                        name: "no_ktp_sim",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "type",
                        name: "type",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "is_kacamata",
                        name: "is_kacamata",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "kondisi_kacamata",
                        name: "kondisi_kacamata",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "kondisi_kacamata_out",
                        name: "kondisi_kacamata_out",
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
                        data: "photo_visitor_out",
                        name: "photo_visitor_out",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "img_visitor",
                        name: "img_visitor",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "waktu_masuk",
                        name: "waktu_masuk",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "waktu_keluar",
                        name: "waktu_keluar",
                        orderable: false,
                        searchable: false,
                    },
                    // {
                    //     data: "action",
                    //     name: "action",
                    // },
                ],
            },
            dataSend: {},
            excludeSearchColumns: [0, 5, 8, 9, 10, 11, 12, 13],
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

                        // // trugger row checkbox
                        // $(document).on("change", ".row-checkbox", function () {
                        //     let customerId = $(this).data("id");
                        //     let isChecked = $(this).prop("checked");

                        //     console.log(
                        //         `Checkbox for customer ID ${customerId} is ${
                        //             isChecked ? "checked" : "unchecked"
                        //         }`
                        //     );
                        // });

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

// Function to destroy and recreate the datatable
function reloadDataTable(filter) {
    console.log("Destroying existing DataTable...");

    // Destroy the current DataTable instance if it exists
    if ($.fn.DataTable.isDataTable(contentDatatable._datatable.className)) {
        $(contentDatatable._datatable.className).DataTable().destroy();
    }

    console.log("Recreating DataTable with new filter:", filter);
    if (!contentDatatable._datatable.dataSend) {
        contentDatatable._datatable.dataSend = { data: {} };
    }
    if (!contentDatatable._datatable.dataSend.data) {
        contentDatatable._datatable.dataSend.data = {};
    }

    // Set the new filter value
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

// Initial datatable load
contentDatatable
    .initialize()
    .then(() => {
        console.log("All datatables initialized successfully");
    })
    .catch((error) => {
        console.error("Initialization failed:", error);
    });

// Tombol Filter ditekan
$("#filter-form").on("submit", function (e) {
    e.preventDefault();

    const formData = $(this).serializeArray();
    let params = {};

    formData.forEach(({ name, value }) => {
        if (value) {
            params[name] = value;
        }
    });

    // Handle tanggal range
    if (params.tanggal_masuk && params.tanggal_masuk.includes(" to ")) {
        const [start, end] = params.tanggal_masuk.split(" to ");
        params.start_date = start;
        params.end_date = end;
        delete params.tanggal_masuk;
    }

    reloadDataTable(params); // Kirim sebagai filter
});

// Reset button
$("#filter-form").on("reset", function () {
    setTimeout(() => {
        reloadDataTable({}); // Reset filter jadi kosong
    }, 100);
});

document.querySelectorAll(".dropdown-item").forEach((item) => {
    item.addEventListener("click", (event) => {
        event.preventDefault();
        const filter = event.target.textContent
            .toLowerCase()
            .replace(/\s/g, "");

        console.log("Selected Filter:", filter);
        document.querySelector(".filter-title").textContent =
            event.target.textContent;
        reloadDataTable(filter);
    });
});
