<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Message</title>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Check if there is an error message -->
    @if(!$type)
        <script>
            // Display SweetAlert error
            Swal.fire({
                title: 'Error!',
                text: "paid failure",  // Get the error message from session
                icon: 'error',  // Use 'error' icon for failure
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <!-- Check if success message exists -->
    @if($type)
        <script>
            // Display SweetAlert success
            Swal.fire({
                title: 'Success!',
                text: "paid success",  // Get the success message from session
                icon: 'success',  // Use 'success' icon
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <!-- Other content of your page goes here -->
</body>
</html>

</html>
