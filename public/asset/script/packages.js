$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".packagesSideA").addClass("activeLi");

    $("#packagesTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchPackagesList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addPackageForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        var formdata = new FormData($("#addPackageForm")[0]);
        $.ajax({
            url: `${domainUrl}addPackage`,
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
                $("#addPackageModal").modal("hide");
                $("#addPackageForm").trigger("reset");
                $("#packagesTable").DataTable().ajax.reload(null, false);
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
    });

    $("#editPackageForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        var formdata = new FormData($("#editPackageForm")[0]);
        $.ajax({
            url: `${domainUrl}editPackage`,
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
                $("#editPackageModal").modal("hide");
                $("#editPackageForm").trigger("reset");
                $("#packagesTable").DataTable().ajax.reload(null, false);
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
    });

    $("#packagesTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var name = $(this).data("name");
        var price = $(this).data("price");
        var days = $(this).data("days");
        var description = $(this).data("description");
        var id = $(this).attr("rel");

        $("#editPackageId").val(id);
        $("#editPackageName").val(name);
        $("#editPackagePrice").val(price);
        $("#editPackageDays").val(days);
        $("#editPackageDescription").val(description);
        $("#editPackageModal").modal("show");
    });

    $("#packagesTable").on("click", ".delete", function (event) {
        event.preventDefault();
        var id = $(this).attr("rel");
        swal({
            title: strings.doYouReallyWantToContinue,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                $.getJSON(`${domainUrl}deletePackage/${id}`).done(function (
                    data
                ) {
                    $("#packagesTable").DataTable().ajax.reload(null, false);
                    iziToast.success({
                        title: strings.success,
                        message: strings.operationSuccessful,
                        position: "topRight",
                    });
                });
            }
        });
    });
});
