$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".contactsSideA").addClass("activeLi");

    $("#contactsTable").dataTable({
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
            url: `${domainUrl}fetchContactsList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });

    $("#addContactForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        
        var formdata = new FormData($("#addContactForm")[0]);
        $.ajax({
            url: `${domainUrl}addContact`,
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
                $("#addContactModal").modal("hide");
                $("#addContactForm").trigger("reset");
                $("#contactsTable").DataTable().ajax.reload(null, false);
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

    $("#editContactForm").on("submit", function (event) {
        event.preventDefault();
        $(".loader").show();
        
        var formdata = new FormData($("#editContactForm")[0]);
        $.ajax({
            url: `${domainUrl}editContact`,
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
                $("#editContactModal").modal("hide");
                $("#editContactForm").trigger("reset");
                $("#contactsTable").DataTable().ajax.reload(null, false);
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

    $("#contactsTable").on("click", ".edit", function (event) {
        event.preventDefault();

        var type = $(this).data("type");
        var value = $(this).data("value");
        var link = $(this).data("link");
        var id = $(this).attr("rel");

        $("#editContactId").val(id);
        $("#editContactType").val(type);
        $("#editContactValue").val(value);
        $("#editContactLink").val(link);
        $("#editContactModal").modal("show");
    });
    
    $("#contactsTable").on("click", ".delete", function (event) {
        event.preventDefault();
        swal({
            title: strings.doYouReallyWantToContinue,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                var id = $(this).attr("rel");
                var url = `${domainUrl}deleteContact` + "/" + id;

                $.getJSON(url).done(function (data) {
                    console.log(data);
                    $("#contactsTable").DataTable().ajax.reload(null, false);
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
