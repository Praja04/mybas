export async function getDatatable(options) {
    return new Promise((resolve, reject) => {
        try {
            const table = $(options.className).DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: options.url,
                    type: options.method,
                    data: function (d) {
                        return { ...d, ...options.dataSend };
                    },
                },
                columns: options.dataColumns.column,
                columnDefs: [
                    {
                        targets: options.excludeSearchColumns,
                        searchable: false,
                    },
                ],
            });

            resolve(table);
        } catch (err) {
            reject(err);
        }
    });
}
