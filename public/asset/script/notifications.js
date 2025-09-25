$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".notificationsSideA").addClass("activeLi");

    // Fetch Sound Categories
    var url = `${domainUrl}getFaqCats`;
    var faqCategories;
    $.getJSON(url).done(function (data) {
        faqCategories = data.data;
    });

    // DataTable للمستخدمين
    $("#usersTable").dataTable({
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
            url: `${domainUrl}fetchUserNotificationList`,
            data: function (data) { },
            error: (error) => {
                console.log(error);
            },
        },
    });

    // DataTable للأطباء
    $("#doctorTable").dataTable({
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
            url: `${domainUrl}fetchDoctorNotificationList`,
            data: function (data) { },
            error: (error) => {
                console.log(error);
            },
        },
    });

    // حذف إشعار الأطباء
    $("#doctorTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteDoctorNotification` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#doctorTable").DataTable().ajax.reload(null, false);
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

    // حذف إشعار المستخدمين
    $("#usersTable").on("click", ".delete", function (event) {
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
                    var url = `${domainUrl}deleteUserNotification` + "/" + id;

                    $.getJSON(url).done(function (data) {
                        console.log(data);
                        $("#usersTable").DataTable().ajax.reload(null, false);
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

    // فتح modal تعديل إشعار الأطباء
    $("#doctorTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var title = $(this).data("title");
        var description = $(this).data("description");
        var id = $(this).attr("rel");

        $("#editDoctorNotiId").val(id);
        $("#editDoctorNotiTitle").val(title);
        $("#editDoctorNotiDesc").val(description);

        $("#editDoctorNotiModal").modal("show");
    });

    // فتح modal تعديل إشعار المستخدمين
    $("#usersTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var title = $(this).data("title");
        var description = $(this).data("description");
        var id = $(this).attr("rel");

        $("#editUserNotiId").val(id);
        $("#editUserNotiTitle").val(title);
        $("#editUserNotiDesc").val(description);

        $("#editUserNotiModal").modal("show");
    });
});