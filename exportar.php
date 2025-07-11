<?php
header("Content-Disposition: attachment; filename=productos.xls");
header("Content-Type: application/vnd.ms-excel");

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
echo "Código	Descripción	Precio	Docena	Cajón	Disponible	Departamento
";
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "{$row['ItemLookupCode']}	{$row['Description']}	{$row['Price']}	{$row['PriceA']}	{$row['PriceB']}	{$row['Quantity']}	{$row['DepartmentID']}
";
}
?>
