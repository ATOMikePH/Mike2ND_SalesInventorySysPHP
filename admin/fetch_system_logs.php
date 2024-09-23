<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "atom_sms";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
    SELECT
    ul.id,
    ul.user_id,
    ul.action_description,
    ul.datetime,
    u.firstname,
    u.lastname,
    ul.system,
    ul.user_name,
    ul.type
FROM
    system_log ul
LEFT JOIN
    users u ON ul.user_id = u.id
ORDER BY
    ul.datetime DESC;
");
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($logs as &$log) {
        $log['user'] = $log['firstname'] . ' ' . substr($log['lastname'], 0, 1) . '.';
    }

    echo json_encode($logs);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>