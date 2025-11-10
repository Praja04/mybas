$(document).ready(function () {
    $(".assign-departement-ga").select2({
        placeholder: "Pilih Departement",
        allowClear: true,
        width: "resolve",
        ajax: {
            url: API_AJAX_GA_LOGISTIK_DEPARTEMENT,
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                };
            },
            processResults: function (data) {
                return {
                    results: data.data.map(function (item) {
                        return {
                            id: item.name,
                            text: item.name,
                        };
                    }),
                };
            },
            cache: true,
        },
    });
});
