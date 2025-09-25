$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".articlesSideA").addClass("activeLi");

    $("#articlesTable").dataTable({
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
            url: `${domainUrl}fetchArticlesList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addArticleForm").on("submit", function (event) {
        console.log('here');
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addArticleForm")[0]);
            $.ajax({
                url: `${domainUrl}addArticle`,
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
                    $("#addArticleModal").modal("hide");
                    $("#addArticleForm").trigger("reset");
                    $("#articlesTable").DataTable().ajax.reload(null, false);
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

    $("#editArticleForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#editArticleForm")[0]);
            $.ajax({
                url: `${domainUrl}editArticle`,
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
                    $("#editArticleModal").modal("hide");
                    $("#editArticleForm").trigger("reset");
                    $("#articlesTable").DataTable().ajax.reload(null, false);
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

    $("#articlesTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var title = $(this).data("title");
        var content = $(this).data("content");
        var heading = $(this).data("heading");
        var id = $(this).attr("rel");

        $("#editArticleId").val(id);
        $("#editArticleTitle").val(title);
        $("#editArticleContent").val(content);
        $("#editArticleHeading").val(heading);
        $("#editArticleModal").modal("show");
    });
    
    $("#articlesTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteArticle` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#articlesTable").DataTable().ajax.reload(null, false);
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
