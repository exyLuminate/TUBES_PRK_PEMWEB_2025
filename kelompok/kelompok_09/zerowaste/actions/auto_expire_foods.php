<?php
function autoExpireFoods($conn, $donatur_id = null) {
    $where_clause = "batas_waktu < NOW() AND status != 'habis' AND deleted_at IS NULL";
    
    if ($donatur_id !== null) {
        $where_clause .= " AND donatur_id = " . (int)$donatur_id;
    }
    
    $sql = "UPDATE food_stocks 
            SET stok_tersedia = 0,
                status = 'habis',
                updated_at = NOW()
            WHERE $where_clause";
    
    try {
        $result = $conn->query($sql);
        
        if ($result) {
            $affected_rows = $conn->affected_rows;
            
            if ($affected_rows > 0) {
                error_log("Auto-expired $affected_rows food items");
            }
            
            return [
                'success' => true,
                'affected_rows' => $affected_rows
            ];
        }
        
        return [
            'success' => false,
            'error' => $conn->error
        ];
        
    } catch (Exception $e) {
        error_log("Auto-expire error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function autoExpireClaims($conn, $donatur_id = null) {
    $sql = "UPDATE claims c
            INNER JOIN food_stocks fs ON c.food_id = fs.id
            SET c.status = 'expired',
                c.updated_at = NOW()
            WHERE fs.batas_waktu < NOW() 
            AND c.status = 'pending'
            AND fs.deleted_at IS NULL";
    
    if ($donatur_id !== null) {
        $sql .= " AND fs.donatur_id = " . (int)$donatur_id;
    }
    
    try {
        $result = $conn->query($sql);
        
        if ($result) {
            $affected_rows = $conn->affected_rows;
            
            if ($affected_rows > 0) {
                error_log("Auto-expired $affected_rows claims");
            }
            
            return [
                'success' => true,
                'affected_rows' => $affected_rows
            ];
        }
        
        return [
            'success' => false,
            'error' => $conn->error
        ];
        
    } catch (Exception $e) {
        error_log("Auto-expire claims error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function runAutoExpire($conn, $donatur_id = null) {
   
    $foods_result = autoExpireFoods($conn, $donatur_id);
    
    $claims_result = autoExpireClaims($conn, $donatur_id);
    
    return [
        'foods' => $foods_result,
        'claims' => $claims_result
    ];
}
?>