<?php
// Fetch products based on the selected supplier ID
if (isset($_POST['supplier_id'])) {
    $supplier_id = $_POST['supplier_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT sp.sp_id, il.id as product_id, il.name as product_name
                            FROM supplier_product sp
                            INNER JOIN item_list il ON sp.product_id = il.id
                            WHERE sp.supplier_id = ? AND sp.status = 1");

    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $products = array();
    while ($row = $result->fetch_assoc()) {
        $products[] = array(
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name']
        );
    }

    echo json_encode(array('products' => $products));

    $stmt->close();
}
?>