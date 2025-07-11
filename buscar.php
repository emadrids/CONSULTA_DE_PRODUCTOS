<?php
$serverName = "launoserver";
$connectionOptions = [
    "Database" => "la1",
    "Uid" => "sa",
    "PWD" => "p@ssw0rd"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}
$busqueda = $_GET["q"] ?? "";
$dpto = $_GET["dpto"] ?? "";

$sql = "
SELECT TOP 100 
    i.ItemLookupCode, i.Description, i.Price, i.PriceA, i.PriceB, i.Quantity, i.DepartmentID
FROM Item i
LEFT JOIN Alias a ON a.ItemID = i.ID
WHERE 
    (i.ItemLookupCode LIKE ? OR i.Description LIKE ? OR a.Alias LIKE ?)";
$params = ["%$busqueda%", "%$busqueda%", "%$busqueda%"];

if ($dpto !== "") {
    $sql .= " AND i.DepartmentID = ?";
    $params[] = $dpto;
}

$stmt = sqlsrv_query($conn, $sql, $params);
$resultados = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $resultados[] = $row;
}
header("Content-Type: application/json");
echo json_encode($resultados, JSON_PRETTY_PRINT);
?>
