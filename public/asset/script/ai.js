$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".aiSideA").addClass("activeLi");

    // Fetch Sound Categories
    var url = `${domainUrl}getDoctorCats`;
    var doctorCategories;
    $.getJSON(url).done(function (data) {
        doctorCategories = data.data;
    });

    $("#questionsTable").dataTable({
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
            url: `${domainUrl}fetchQuestionsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addQuestionForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#addQuestionForm")[0]);
            $.ajax({
                url: `${domainUrl}addQuestion`,
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
                    $("#addQuestionModal").modal("hide");
                    $("#addQuestionForm").trigger("reset");
                    $("#QuestionsTable").DataTable().ajax.reload(null, false);
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

    $("#questionsTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var question = $(this).data("question");
        var catId = $(this).data("cat");
        var id = $(this).attr("rel");
        var choices = $(this).data('choices').split('|');

        $("#editQuestionId").val(id);
        $("#editQuestion").val(question);
        $("#editQuestionCategory").empty();
        
        for (var i = 1; i <= choices.length; i++) {
            $("#editChoice" + i).val(choices[i - 1]);
        }

        $.each(doctorCategories, function (indexInArray, category) {
            console.log(category.title);

            $("#editQuestionCategory").append(`
                    <option ${
                        category.id == catId ? "selected" : ""
                    } value="${category.id}">${category.title}</option>
                `);
        });

        $("#editQuestionModal").modal("show");
    });

    $("#editQuestionForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        if (user_type == "1") {
            var formdata = new FormData($("#editQuestionForm")[0]);
            $.ajax({
                url: `${domainUrl}editQuestion`,
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
                    $("#editQuestionModal").modal("hide");
                    $("#editQuestionForm").trigger("reset");
                    $("#questionsTable").DataTable().ajax.reload(null, false);
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

    $("#questionsTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteQuestion` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#questionsTable").DataTable().ajax.reload(null, false);
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
