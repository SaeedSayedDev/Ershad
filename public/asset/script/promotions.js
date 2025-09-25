$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".promotionsSideA").addClass("activeLi");

    $("#allPromotionsTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchAllPromotionsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#pendingPromotionTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchPendingPromotionsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#approvedPromotionTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchApprovedPromotionsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#rejectedPromotionTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchRejectedPromotionsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#pendingPromotionTable").on("click", ".approve", function (event) {
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
                    var url = `${domainUrl}approvePromotion` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#pendingPromotionTable").DataTable().ajax.reload(null, false);
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

    $("#pendingPromotionTable").on("click", ".reject", function (event) {
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
                    $("#promotionId").val(id);
                    $("#addRejectionReasonModal").modal("show");
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

    $("#addRejectionReasonForm").on("submit", function (event) {
        console.log("here");
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addRejectionReasonForm")[0]);
            $.ajax({
                url: `${domainUrl}rejectPromotion`,
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
                    $("#addRejectionReasonModal").modal("hide");
                    $("#addRejectionReasonForm").trigger("reset");
                    $("#pendingPromotionTable")
                        .DataTable()
                        .ajax.reload(null, false);
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
});
