$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".promoCodesSideA").addClass("activeLi");

    $("#promoCodesTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        ajax: {
            url: `${domainUrl}fetchAllPromoCodesList`,
            data: function (data) { },
            error: (error) => {
                console.log(error);
            },
        },
        columnDefs: [
            { targets: 5, visible: false, searchable: false } // إخفاء user_id
        ]
    });


    $("#addPromoCodeForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addPromoCodeForm")[0]);
            $.ajax({
                url: `${domainUrl}addPromoCodeItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (!response.status) {
                        return iziToast.error({
                            title: strings.error,
                            message: response.message,
                            position: "topRight",
                        });
                    }
                    $(".loader").hide();
                    $("#addPromoCodeModal").modal("hide");
                    $("#addPromoCodeForm").trigger("reset");
                    $("#promoCodesTable").DataTable().ajax.reload(null, false);
                    iziToast.success({
                        title: strings.success,
                        message: strings.operationSuccessful,
                        position: "topRight",
                    });
                },
                error: (error) => {
                    $(".loader").hide();
                    console.log(JSON.stringify(error));
                },
            });
        } else {
            $(".loader").hide();
            iziToast.error({
                title: strings.error,
                message: strings.youAreTester,
                position: "topRight",
            });
        }
    });

    $("#editPromoCodeForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#editPromoCodeForm")[0]);
            $.ajax({
                url: `${domainUrl}editPromoCodeItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (!response.status) {
                        return iziToast.error({
                            title: strings.error,
                            message: response.message,
                            position: "topRight",
                        });
                    }
                    $(".loader").hide();
                    $("#editPromoCodeModal").modal("hide");
                    $("#editPromoCodeForm").trigger("reset");
                    $("#promoCodesTable").DataTable().ajax.reload(null, false);
                    iziToast.success({
                        title: strings.success,
                        message: strings.operationSuccessful,
                        position: "topRight",
                    });
                },
                error: (error) => {
                    $(".loader").hide();
                    console.log(JSON.stringify(error));
                },
            });
        } else {
            $(".loader").hide();
            iziToast.error({
                title: strings.error,
                message: strings.youAreTester,
                position: "topRight",
            });
        }
    });

    $("#promoCodesTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var maxDiscAmount = $(this).data("maxdiscamount");
        var percentage = $(this).data("percentage");
        var code = $(this).data("code");
        var description = $(this).data("description");
        var heading = $(this).data("heading");
        var expiredAt = $(this).data("expiredat");
        var id = $(this).attr("rel");

        $("#editPromoCodeId").val(id);
        $("#editMaxDiscAmount").val(maxDiscAmount);
        $("#editCode").val(code);
        $("#editHeading").val(heading);
        $("#editDescription").val(description);
        $("#editPercentage").val(percentage);
        $("#editExpiredAt").val(expiredAt);

        $("#editPromoCodeModal").modal("show");
    });

    $("#promoCodesTable").on("click", ".delete", function (event) {
        event.preventDefault();
        swal({
            title: strings.doYouReallyWantToContinue,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                if (user_type == "1") {
                    var id = $(this).attr("rel");
                    var url = `${domainUrl}deletePromoCode` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#promoCodesTable")
                            .DataTable()
                            .ajax.reload(null, false);
                        iziToast.success({
                            title: strings.success,
                            message: strings.operationSuccessful,
                            position: "topRight",
                        });
                    });
                } else {
                    iziToast.error({
                        title: strings.error,
                        message: strings.youAreTester,
                        position: "topRight",
                    });
                }
            }
        });
    });
});
