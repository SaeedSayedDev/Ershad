$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".appointmentsSideA").addClass("activeLi");

    // Initialize all DataTables with capital D
    var allTable = $("#allAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchAllAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('All table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column - last column
                render: function (data, type, row) {

                    // Get the appointment ID from the last element
                    const appointmentId = row[row.length - 1];
                    // Assuming ID is at index 13
                    return `
                       <div class="action-buttons">
                         ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                },
            },
           
        ]
    });

    var pendingTable = $("#pendingAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchPendingAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('Pending table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column
                render: function (data, type, row) {
                    // هنا هيتطبع كل بيانات الصف

                    const appointmentId = row[row.length - 1];
                    return `
                       <div class="action-buttons">
                         ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    var acceptedTable = $("#acceptedAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchAcceptedAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('Accepted table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column
                render: function (data, type, row) {
                    const appointmentId = row[row.length - 1];
                    return `
                       <div class="action-buttons">
                         ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    var completedTable = $("#completedAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchCompletedAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('Completed table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column
                render: function (data, type, row) {
                    const appointmentId = row[row.length - 1];
                    return `
                        <div class="action-buttons">
                            ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    var cancelledTable = $("#cancelledAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchCancelledAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('Cancelled table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column
                render: function (data, type, row) {
                    const appointmentId = row[row.length - 1];
                    return `
                        <div class="action-buttons">
                  ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    var declinedTable = $("#declinedAppointmentTable").DataTable({
        dom: "Bfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        processing: true,
        serverSide: true,
        ajax: {
            url: `${domainUrl}fetchDeclinedAppointmentsList`,
            type: "POST",
            error: function (error) {
                console.log('Declined table error:', error);
            }
        },
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                orderable: false,
            },
            {
                targets: [11], // Action column
                render: function (data, type, row) {
                    const appointmentId = row[row.length - 1];
                    return `
                        <div class="action-buttons">
                        ${data}
                            <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editAppointment('${appointmentId}')">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteAppointment('${appointmentId}')">
                                <i class="fa fa-trash"></i> 
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Add Appointment Form Handler - FIXED
    $('#addAppointmentForm').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: `${domainUrl}AddAppointmentsForAdmin`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log('Add response:', response);
                if (response.status) {
                    toastr.success(response.message || 'Appointment added successfully');
                    $('#addAppointmentModal').modal('hide');
                    $('#addAppointmentForm')[0].reset();

                    // Force refresh all tables
                    window.location.reload();
                } else {
                    toastr.error(response.message || 'Error occurred');
                }
            },
            error: function (xhr) {
                console.log('Add error:', xhr.responseText);
                toastr.error('Something went wrong!');
            }
        });
    });

    // Calculate totals when amounts change
    $('#service_amount, #discount_amount, #total_tax_amount').on('input', function () {
        calculateTotals();
    });

    $('#edit_service_amount, #edit_discount_amount, #edit_total_tax_amount').on('input', function () {
        calculateEditTotals();
    });

    // Show/Hide coupon title field
    $('#is_coupon_applied').on('change', function () {
        if ($(this).val() == '1') {
            $('#coupon_title_group').show();
            $('#coupon_title').attr('required', true);
        } else {
            $('#coupon_title_group').hide();
            $('#coupon_title').attr('required', false);
        }
    });

    $('#edit_is_coupon_applied').on('change', function () {
        if ($(this).val() == '1') {
            $('#edit_coupon_title_group').show();
            $('#edit_coupon_title').attr('required', true);
        } else {
            $('#edit_coupon_title_group').hide();
            $('#edit_coupon_title').attr('required', false);
        }
    });
});

// Calculate totals for add form
function calculateTotals() {
    const serviceAmount = parseFloat($('#service_amount').val()) || 0;
    const discountAmount = parseFloat($('#discount_amount').val()) || 0;
    const taxAmount = parseFloat($('#total_tax_amount').val()) || 0;

    const subtotal = serviceAmount - discountAmount;
    const payableAmount = subtotal + taxAmount;

    $('#subtotal').val(subtotal.toFixed(2));
    $('#payable_amount').val(payableAmount.toFixed(2));
}

// Calculate totals for edit form
function calculateEditTotals() {
    const serviceAmount = parseFloat($('#edit_service_amount').val()) || 0;
    const discountAmount = parseFloat($('#edit_discount_amount').val()) || 0;
    const taxAmount = parseFloat($('#edit_total_tax_amount').val()) || 0;

    const subtotal = serviceAmount - discountAmount;
    const payableAmount = subtotal + taxAmount;

    $('#edit_subtotal').val(subtotal.toFixed(2));
    $('#edit_payable_amount').val(payableAmount.toFixed(2));
}

// FIXED: Refresh all DataTables
function refreshAllTables() {
    console.log('Refreshing all tables...');

    try {
        // Force reload each table
        $('#allAppointmentTable').DataTable().ajax.reload(null, false);
        $('#pendingAppointmentTable').DataTable().ajax.reload(null, false);
        $('#acceptedAppointmentTable').DataTable().ajax.reload(null, false);
        $('#completedAppointmentTable').DataTable().ajax.reload(null, false);
        $('#cancelledAppointmentTable').DataTable().ajax.reload(null, false);
        $('#declinedAppointmentTable').DataTable().ajax.reload(null, false);

        console.log('All tables refreshed successfully');
    } catch (error) {
        console.log('Error refreshing tables:', error);
        // Fallback: reload the page
        setTimeout(function () {
            window.location.reload();
        }, 1000);
    }
}

// Placeholder functions for edit and delete
function editAppointment(appointmentId) {
    console.log('Edit appointment:', appointmentId);
    // Add your edit logic here
}

function deleteAppointment(appointmentId) {
    console.log('Delete appointment:', appointmentId);
    // Add your delete logic here
}



// Edit Appointment Function - UPDATED
function editAppointment(appointmentId) {
    $.ajax({
        url: `${domainUrl}getAppointmentForEdit/${appointmentId}`,
        type: 'GET',
        success: function (response) {
            if (response.status) {
                const appointment = response.data;

                // Fill the edit form with appointment data
                $('#edit_appointment_id').val(appointment.id);
                $('#edit_user_id').val(appointment.user_id);
                $('#edit_doctor_id').val(appointment.doctor_id);
                $('#edit_date').val(appointment.date);
                $('#edit_time').val(appointment.time.replace(/(\d{2})(\d{2})/, '$1:$2')); // Format time
                $('#edit_type').val(appointment.type);
                $('#edit_session_type').val(appointment.session_type);
                $('#edit_problem').val(appointment.problem);
                $('#edit_service_amount').val(appointment.service_amount);
                $('#edit_discount_amount').val(appointment.discount_amount);
                $('#edit_total_tax_amount').val(appointment.total_tax_amount);
                $('#edit_subtotal').val(appointment.subtotal);
                $('#edit_payable_amount').val(appointment.payable_amount);
                $('#edit_status').val(appointment.status);
                $('#edit_is_urgent').val(appointment.is_urgent);
                $('#edit_is_coupon_applied').val(appointment.is_coupon_applied);

                if (appointment.is_coupon_applied == 1) {
                    $('#edit_coupon_title_group').show();
                    $('#edit_coupon_title').val(appointment.coupon_title);
                } else {
                    $('#edit_coupon_title_group').hide();
                }

                $('#editAppointmentModal').modal('show');
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            toastr.error('Failed to fetch appointment data!');
            console.log(xhr.responseText);
        }
    });
}
$('#editAppointmentForm').on('submit', function (e) {
    e.preventDefault();

    var appointmentId = $('#edit_appointment_id').val();
    var formData = new FormData(this);

    $.ajax({
        url: `${domainUrl}updateAppointmentForAdmin/${appointmentId}`,
        type: 'POST', // خليه POST مش PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status) {
                toastr.success(response.message || 'Appointment updated successfully');
                $('#editAppointmentModal').modal('hide');
                window.location.reload();
            } else {
                toastr.error(response.message || 'Error occurred');
            }
        },
        error: function (xhr) {
            toastr.error('Something went wrong!');
            console.log(xhr.responseText);
        }
    });
});



// Edit Appointment Form Handler
$(document).on('submit', '#editAppointmentForm', function (e) {
    e.preventDefault();

    var appointmentId = $('#edit_appointment_id').val();
    var formData = new FormData(this);

    $.ajax({
        url: `${domainUrl}updateAppointmentForAdmin/${appointmentId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status) {
                toastr.success(response.message || 'Appointment updated successfully');
                $('#editAppointmentModal').modal('hide');
                window.location.reload();
            } else {
                toastr.error(response.message || 'Error occurred');
            }
        },
        error: function (xhr) {
            toastr.error('Something went wrong!');
            console.log(xhr.responseText);
        }
    });
});

// إضافة هذه الكودات إلى ملف appointments.js الموجود

// Delete Appointment Function - NEW
function deleteAppointment(appointmentId) {
    console.log('Delete appointment:', appointmentId);

    // Set the appointment ID in the hidden field
    $('#delete_appointment_id').val(appointmentId);

    // Show the delete confirmation modal
    $('#deleteAppointmentModal').modal('show');
}

// Delete Appointment Form Handler - NEW
$(document).on('submit', '#deleteAppointmentForm', function (e) {
    e.preventDefault();

    var appointmentId = $('#delete_appointment_id').val();

    $.ajax({
        url: `${domainUrl}deleteAppointmentForAdmin/${appointmentId}`,
        type: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            console.log('Delete response:', response);
            if (response.status) {
                toastr.success(response.message || 'Appointment deleted successfully');
                $('#deleteAppointmentModal').modal('hide');

                // Force refresh all tables
                window.location.reload();
            } else {
                toastr.error(response.message || 'Error occurred');
            }
        },
        error: function (xhr) {
            console.log('Delete error:', xhr.responseText);
            toastr.error('Something went wrong!');
        }
    });
});

// Alternative method using form action (if you prefer)
function deleteAppointmentAlternative(appointmentId) {
    console.log('Delete appointment:', appointmentId);

    // Set the form action URL
    $('#deleteAppointmentForm').attr('action', `${domainUrl}deleteAppointmentForAdmin/${appointmentId}`);

    // Set the appointment ID in the hidden field
    $('#delete_appointment_id').val(appointmentId);

    // Show the delete confirmation modal
    $('#deleteAppointmentModal').modal('show');
}
