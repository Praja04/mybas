export async function getDatatable(options) {
    return new Promise((resolve, reject) => {
        try {
            const table = $(options.className).DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                language: options.language || {},
                ajax: {
                    url: options.url,
                    type: options.method,
                    data: function (d) {
                        return { ...d, ...options.dataSend };
                    },
                },
                columns: options.dataColumns.column,
                columnDefs: [
                    ...(options.excludeSearchColumns
                        ? [
                              {
                                  targets: options.excludeSearchColumns,
                                  searchable: false,
                              },
                          ]
                        : []),
                    ...(options.columnDefs || []),
                ],
            });

            resolve(table);
        } catch (err) {
            reject(err);
        }
    });
}
