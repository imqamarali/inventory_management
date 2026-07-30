<!DOCTYPE html>
<html>
<head>
    <title>SweetAlert2 Test</title>
    <link rel="stylesheet" href="/inventory_system/web/css/sweetalert2.min.css">
</head>
<body>
    <h1>SweetAlert2 Test</h1>
    <button onclick="testSwal()">Click to test Swal</button>

    <script src="/inventory_system/web/js/sweetalert2.all.min.js"></script>
    <script>
        function testSwal() {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Success', 'SweetAlert2 is working!', 'success');
            } else {
                alert('Swal is not loaded');
            }
        }

        // Check on page load
        window.addEventListener('load', function() {
            console.log('Swal type:', typeof Swal);
            console.log('Swal object:', Swal);
        });
    </script>
</body>
</html>
