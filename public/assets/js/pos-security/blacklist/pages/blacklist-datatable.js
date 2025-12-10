import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-blacklist-datatables",
            url: API_DATATABLE_BLACKLIST_SUPPLIER,
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
                        data: "nama",
                        name: "nama",
                    },
                    {
                        data: "no_identitas",
                        name: "no_identitas",
                    },
                    {
                        data: "jenis_identitas",
                        name: "jenis_identitas",
                    },
                    {
                        data: "tanggal_lahir",
                        name: "tanggal_lahir",
                    },
                    {
                        data: "alasan_blacklist",
                        name: "alasan_blacklist",
                    },
                    {
                        data: "tanggal_blacklist",
                        name: "tanggal_blacklist",
                    },
                    {
                        data: "diblacklist_oleh",
                        name: "diblacklist_oleh",
                    },
                    {
                        data: "aktif",
                        name: "aktif",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
            },
            dataSend: {},
            excludeSearchColumns: [0, 9],

            // columnVisibility: {
            //     small: [0, 1, 6], // Show only columns 1 and 7 on small screens
            //     medium: [0, 1, 2, 3, 7], // Show all columns on medium screens
            //     large: [0, 1, 2, 3, 4, 5, 6], // Show all columns on large screens
            // },
            // // Kolom yang harus menggunakan Select2
            // select2Columns: [4], // Misalnya kolom status pakai Select2

            // // Kolom yang harus berisi checkbox
            // checkboxColumns: [7], // Misalnya kolom action berisi checkbox
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
