<?php
/*
 * Name: Nguyễn Huyền My
 * Student ID: 23070626
 * Date: 2026-11-14
 * Assignment: Advanced PHP Calculator with Exception Handling
 */

session_start();
error_reporting(E_ALL);

// ==========================================
// 1. CUSTOM EXCEPTION CLASSES
// ==========================================
class DivisionByZeroException extends Exception {}
class InvalidOperatorException extends Exception {}
class InvalidInputException extends Exception {}

// Khởi tạo lịch sử tính toán trong Session
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

$num1 = '';
$num2 = '';
$op = '+';
$result_message = '';
$message_type = ''; // 'success', 'error'

// ==========================================
// 2. XỬ LÝ FORM SUBMIT
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Nút: Xóa lịch sử
    if (isset($_POST['clear_history'])) {
        $_SESSION['history'] = [];
        $result_message = "Đã xóa toàn bộ lịch sử tính toán.";
        $message_type = "success";
    } 
    // Nút: Tính toán
    elseif (isset($_POST['calculate'])) {
        // Lấy dữ liệu và giữ lại cho Sticky Form
        $num1_raw = $_POST['num1'] ?? '';
        $num2_raw = $_POST['num2'] ?? '';
        $op = $_POST['op'] ?? '+';
        
        $num1 = $num1_raw;
        $num2 = $num2_raw;

        try {
            // Validation 1: Kiểm tra số hợp lệ (Non-numeric input)
            if (!is_numeric($num1_raw) || !is_numeric($num2_raw)) {
                throw new InvalidInputException("Dữ liệu không phải là số hợp lệ.");
            }

            $n1 = (float)$num1_raw;
            $n2 = (float)$num2_raw;

            // Validation 2: Kiểm tra toán tử
            $valid_ops = ['+', '-', '*', '/', '%', '**'];
            if (!in_array($op, $valid_ops)) {
                throw new InvalidOperatorException("Phép toán '$op' không được hỗ trợ.");
            }

            // Validation 3: Lỗi chia cho 0
            if (($op === '/' || $op === '%') && $n2 == 0) {
                throw new DivisionByZeroException("Không thể thực hiện phép tính chia hoặc lấy dư cho 0.");
            }

            // Thực hiện tính toán
            $res = 0;
            switch ($op) {
                case '+': $res = $n1 + $n2; break;
                case '-': $res = $n1 - $n2; break;
                case '*': $res = $n1 * $n2; break;
                case '/': $res = $n1 / $n2; break;
                case '%': $res = $n1 % $n2; break;
                case '**': $res = $n1 ** $n2; break;
            }

            // Validation 4: Tràn bộ nhớ (Overflow)
            if (is_infinite($res) || is_nan($res)) {
                throw new OverflowException("Kết quả vượt quá giới hạn tính toán của PHP.");
            }

            // Định dạng kết quả đầu ra
            if ($op === '/') {
                $formatted_res = number_format($res, 4, '.', ''); // 4 số thập phân cho phép chia
            } else {
                $formatted_res = (int)round($res); // Hiển thị số nguyên cho các phép tính khác
            }

            $expression = "$n1 $op $n2";
            $result_str = "$expression = $formatted_res";

            // Lưu vào Session History
            $_SESSION['history'][] = [
                'expression' => $expression,
                'result' => $formatted_res,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $result_message = $result_str;
            $message_type = "success";

        } catch (Exception $e) {
            // Xử lý Ghi Log (Error Logging)
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $date = date('Y-m-d H:i:s');
            $type = get_class($e);
            $msg = $e->getMessage();
            
            // Lọc dữ liệu đầu vào chống XSS khi ghi log
            $input_log = htmlspecialchars("$num1_raw $op $num2_raw");
            
            $logLine = "[$date] $type: $msg | Input: \"$input_log\" | IP: $ip" . PHP_EOL;
            file_put_contents('calculator_errors.log', $logLine, FILE_APPEND);

            // Thông báo lỗi thân thiện cho người dùng
            $result_message = "Đã xảy ra lỗi: " . $msg . " Vui lòng nhập lại!";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced PHP Calculator</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .calculator-container { max-width: 500px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { display: flex; gap: 10px; margin-bottom: 15px; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; }
        select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; font-weight: bold; }
        .btn-group { display: flex; gap: 10px; margin-bottom: 20px; }
        button { flex: 1; padding: 12px; font-size: 16px; font-weight: bold; border: none; border-radius: 4px; cursor: pointer; }
        .btn-calc { background-color: #0d6efd; color: white; }
        .btn-calc:hover { background-color: #0b5ed7; }
        .btn-clear { background-color: #6c757d; color: white; }
        .btn-clear:hover { background-color: #5c636a; }
        
        /* Error/Success Boxes */
        .message-box { padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .error { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        
        /* History Table */
        .history-box { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: center; }
        th { background-color: #f1f3f5; }
    </style>
</head>
<body>

<div class="calculator-container">
    <h2>Máy tính PHP Câng cao</h2>

    <!-- Hộp hiển thị Kết quả / Lỗi -->
    <?php if (!empty($result_message)): ?>
        <div class="message-box <?= $message_type === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($result_message) ?>
        </div>
    <?php endif; ?>

    <!-- Form Tính toán (Sticky Form) -->
    <form method="POST" action="calculator.php">
        <div class="form-group">
            <input type="text" name="num1" value="<?= htmlspecialchars($num1) ?>" placeholder="Số thứ nhất">
            <select name="op">
                <option value="+" <?= $op === '+' ? 'selected' : '' ?>>+</option>
                <option value="-" <?= $op === '-' ? 'selected' : '' ?>>-</option>
                <option value="*" <?= $op === '*' ? 'selected' : '' ?>>*</option>
                <option value="/" <?= $op === '/' ? 'selected' : '' ?>>/</option>
                <option value="%" <?= $op === '%' ? 'selected' : '' ?>>%</option>
                <option value="**" <?= $op === '**' ? 'selected' : '' ?>>**</option>
            </select>
            <input type="text" name="num2" value="<?= htmlspecialchars($num2) ?>" placeholder="Số thứ hai">
        </div>
        <div class="btn-group">
            <button type="submit" name="calculate" class="btn-calc">Tính toán</button>
            <button type="submit" name="clear_history" class="btn-clear" formnovalidate>Xóa lịch sử</button>
        </div>
    </form>

    <!-- Bảng Lịch sử Tính toán -->
    <div class="history-box">
        <h3>Lịch sử phép tính</h3>
        <?php if (!empty($_SESSION['history'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Phép tính</th>
                        <th>Kết quả</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Đảo ngược mảng để hiển thị kết quả mới nhất lên đầu -->
                    <?php foreach (array_reverse($_SESSION['history']) as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['timestamp']) ?></td>
                        <td><?= htmlspecialchars($record['expression']) ?></td>
                        <td><strong><?= htmlspecialchars($record['result']) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #6c757d;">Chưa có dữ liệu lịch sử.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>