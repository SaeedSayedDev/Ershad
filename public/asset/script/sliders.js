$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".slidersSideA").addClass("activeLi");

    $("#slidersTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchAllslidersList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addsliderForm").on("submit", function (event) {
        console.log('here');
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addsliderForm")[0]);
            $.ajax({
                url: `${domainUrl}addsliderItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $(".loader").hide();
                    $("#addsliderModal").modal("hide");
                    $("#addsliderForm").trigger("reset");
                    $("#slidersTable").DataTable().ajax.reload(null, false);
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
    $("#editsliderForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#editsliderForm")[0]);
            $.ajax({
                url: `${domainUrl}editsliderItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $(".loader").hide();
                    $("#editsliderModal").modal("hide");
                    $("#editsliderForm").trigger("reset");
                    $("#slidersTable").DataTable().ajax.reload(null, false);
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

    $("#slidersTable").on("click", ".edit", function (event) {
        event.preventDefault();

      
        var slider = $(this).data("title");
        var link = $(this).data("link");
        var heading = $(this).data("heading");
        var id = $(this).attr("rel");

        $("#editsliderId").val(id);
        $("#editslider").val(slider);
        $("#editlink").val(link);
        $("#editHeading").val(heading);
        $("#editsliderModal").modal("show");
    });
    $("#slidersTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteslider` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#slidersTable").DataTable().ajax.reload(null, false);
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
