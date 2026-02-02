import { getDatatable } from "../../../shared/services/datatable.services.js";

export class ContentDatatable {
    constructor() {
        this._datatable = {
            className: ".ga-history-supplier-pas-datatables",
            url: API_DATATABLE_HISTORY_SUPPLIER,
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
                        data: "namavisitor",
                        name: "namavisitor",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "keterangan",
                        name: "keterangan",
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
                        data: "nopol",
                        name: "nopol",
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: "no_kartu",
                        name: "no_kartu",
                        orderable: true,
                        searchable: true,
                    },
                    // {
                    //     data: "is_kacamata",
                    //     name: "is_kacamata",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                    // {
                    //     data: "kondisi_kacamata",
                    //     name: "kondisi_kacamata",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                    // {
                    //     data: "kondisi_kacamata_out",
                    //     name: "kondisi_kacamata_out",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                    // {
                    //     data: "photo_visitor",
                    //     name: "photo_visitor",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                    // {
                    //     data: "photo_visitor_out",
                    //     name: "photo_visitor_out",
                    //     orderable: false,
                    //     searchable: false,
                    // },
                    // {
                    //     data: "img_visitor",
                    //     name: "img_visitor",
                    //     orderable: false,
                    //     searchable: false,
                    // },
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
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
            },
            // priority column
            columnDefs: [
                {
                    targets: 1, // kolom Nama Supir/Kernet
                    responsivePriority: 1,
                },
                {
                    targets: 3, // kolom Nama Perusahaan  
                    responsivePriority: 2,
                },
                {
                    targets: -3, // kolom Waktu Masuk
                    responsivePriority: 3,
                },
                {
                    targets: -2, // kolom Waktu Keluar
                    responsivePriority: 4,
                },
                {
                    targets: -1, // kolom Action
                    responsivePriority: 5,
                },
            ],
            dataSend: {},
            excludeSearchColumns: [0, 2, -3, -2, -1],
        };
    }

    initialize() {
        return new Promise((resolve, reject) => {
            $(() => {
                getDatatable(this._datatable)
                    .then((table) => {
                        console.log("datatable content initialized");
                        this.table = table; // ⬅️ PENTING
                        resolve();
                    })
                    .catch(reject);
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

// Tombol Filter ditekan
$("#filter-form").on("submit", function (e) {
    e.preventDefault();

    let params = {};
    $(this).serializeArray().forEach(({ name, value }) => {
        if (value) params[name] = value;
    });

    // Handle tanggal range
    if (params.tanggal_masuk) {
        
        if (params.tanggal_masuk.includes(" to ")) {
            // RANGE
            const [start, end] = params.tanggal_masuk.split(" to ");
            params.start_date = start;
            params.end_date = end;
        } else {
            // SATU TANGGAL
            params.start_date = params.tanggal_masuk;
            
        }

        delete params.tanggal_masuk;
    }

    contentDatatable._datatable.dataSend = params;
    contentDatatable.table.ajax.reload();
});


// Reset button
$("#filter-form").on("reset", function () {
    setTimeout(() => {
        contentDatatable._datatable.dataSend = {};
        contentDatatable.table.ajax.reload();
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
            
        contentDatatable._datatable.dataSend = { status: filter };
        contentDatatable.table.ajax.reload();
    });
});
