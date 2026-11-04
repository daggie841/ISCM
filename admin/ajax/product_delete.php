<?php
require __DIR__ . '/../../core/bootstrap.php';
guard_ajax('admin', ['admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Invalid request.', 405);

$id = (int)(request_input()['id'] ?? 0);
$p = db_one('SELECT id, sku FROM products WHERE id = ?', [$id]);
if (!$p) json_fail('Product not found.', 404);

// Products used in sales or purchase orders keep their history, so they can only be deactivated.
$used = db_val('SELECT (SELECT COUNT(*) FROM sale_items WHERE product_id = ?) + (SELECT COUNT(*) FROM po_items WHERE product_id = ?)
                + (SELECT COUNT(*) FROM receipt_items WHERE product_id = ?)', [$id, $id, $id]);
if ((int)$used > 0) json_fail('This product has sales or purchase history, so it cannot be deleted. Deactivate it instead.');

$pdo = db();
$pdo->beginTransaction();
try {
    foreach (['stock_movements', 'stock', 'supplier_prices', 'forecasts', 'model_metrics', 'reorder_recommendations'] as $t) {
        db_query("DELETE FROM $t WHERE product_id = ?", [$id]);
    }
    db_query('DELETE FROM alerts WHERE product_id = ?', [$id]);
    db_query('DELETE FROM products WHERE id = ?', [$id]);
    $pdo->commit();
} catch (Throwable $ex) {
    $pdo->rollBack();
    log_error('admin', 'product_delete failed: ' . $ex->getMessage(), ['id' => $id]);
    json_fail('Could not delete the product. The error was logged.', 500);
}
log_activity('product_deleted', $p['sku']);
json_ok(['message' => 'Product deleted.']);
