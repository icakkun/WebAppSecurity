<?php
$conn = @mysqli_connect('localhost', 'root', '');
if ($conn) {
    echo "SUCCESS\n";
    mysqli_close($conn);
} else {
    echo "FAIL: " . mysqli_connect_error() . "\n";
}
?>
