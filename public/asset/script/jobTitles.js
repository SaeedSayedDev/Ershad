$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".jobTitlesSideA").addClass("activeLi");

    $("#jobTitlesTable").dataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}fetchAllJobTitlesList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addjobTitleForm").on("submit", function (event) {
        console.log('here');
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addjobTitleForm")[0]);
            $.ajax({
                url: `${domainUrl}addJobTitlesItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $(".loader").hide();
                    $("#addjobTitleModal").modal("hide");
                    $("#addjobTitleForm").trigger("reset");
                    $("#jobTitlesTable").DataTable().ajax.reload(null, false);
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
    $("#editjobTitleForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#editjobTitleForm")[0]);
            $.ajax({
                url: `${domainUrl}editJobTitlesItem`,
                type: "POST",
                data: formdata,
                dataType: "json",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    $(".loader").hide();
                    $("#editjobTitleModal").modal("hide");
                    $("#editjobTitleForm").trigger("reset");
                    $("#jobTitlesTable").DataTable().ajax.reload(null, false);
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

    $("#jobTitlesTable").on("click", ".edit", function (event) {
        event.preventDefault();

      
        var jobTitle = $(this).data("name");
        var id = $(this).attr("rel");

        $("#editjobTitleId").val(id);
        $("#editjobTitle").val(jobTitle);
        $("#editjobTitleModal").modal("show");
    });
    $("#jobTitlesTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteJobTitles` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#jobTitlesTable").DataTable().ajax.reload(null, false);
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
