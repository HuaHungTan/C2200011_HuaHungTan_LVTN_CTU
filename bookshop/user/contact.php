<?php
session_start();
require('../database/conn.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$admin_id = 1;

// Lấy avatar & tên
$user_info = $conn->query("SELECT name, avt FROM users WHERE user_id = $user_id")->fetch_assoc();
$admin_info = $conn->query("SELECT name, avt FROM users WHERE user_id = $admin_id")->fetch_assoc();

// Gửi tin nhắn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $message = trim($_POST['message'] ?? '');
  if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $admin_id, $message);
    $stmt->execute();
    $stmt->close();
    header("Location: contact.php");
    exit;
  }
}
// cập nhật trạng thái tin nhắn khi truy cập
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $admin_id = 1;

    $conn->query("UPDATE messages SET is_read = 1
                  WHERE sender_id = $admin_id AND receiver_id = $user_id AND is_read = 0");
}
include("includes/header.php");
?>

<div class="container my-5">
  <h2 class="fw-bold text-center text-primary mb-4">💬 Liên hệ Chăm sóc khách hàng</h2>

  <div class="chat-box border rounded p-3 mb-4" style="height:400px; overflow-y:auto; background:#f8f9fa;">
    <?php
    $messages = $conn->query("
      SELECT * FROM messages
      WHERE (sender_id = $user_id AND receiver_id = $admin_id)
         OR (sender_id = $admin_id AND receiver_id = $user_id)
      ORDER BY sent_at ASC
    ");

    while ($row = $messages->fetch_assoc()) {
      $isMine = $row['sender_id'] == $user_id;

      $alignClass = 'd-flex mb-3'; // không cần justify-content ở ngoài, sẽ dùng flex bên trong
      $bubbleStyle = $isMine ? 'bg-primary text-white border-end' : 'bg-light text-dark border-start';
      $avatarUrl = $isMine ? $user_info['avt'] : $admin_info['avt'];
      $nameLabel = $isMine ? $user_info['name'] : $admin_info['name'];

      echo "<div class='$alignClass " . ($isMine ? 'justify-content-end text-end' : 'justify-content-start text-start') . "'>
              " . ($isMine ? "
                <div>
                  <div class='small text-muted'>" . htmlspecialchars($nameLabel) . " • " . date('H:i d/m/Y', strtotime($row['sent_at'])) . "</div>
                  <span class='d-inline-block px-3 py-2 rounded $bubbleStyle' style='max-width:75%;'>
                    " . htmlspecialchars($row['content']) . "
                  </span>
                </div>
                <img src='../$avatarUrl' class='rounded-circle ms-2' style='width:36px;height:36px;object-fit:cover;'>
              " : "
                <img src='../$avatarUrl' class='rounded-circle me-2' style='width:36px;height:36px;object-fit:cover;'>
                <div>
                  <div class='small text-muted'>" . htmlspecialchars($nameLabel) . " • " . date('H:i d/m/Y', strtotime($row['sent_at'])) . "</div>
                  <span class='d-inline-block px-3 py-2 rounded $bubbleStyle' style='max-width:75%;'>
                    " . htmlspecialchars($row['content']) . "
                  </span>
                </div>
              ") . "
            </div>";
    }
    ?>
  </div>

  <form method="post">
    <div class="input-group">
      <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
      <button type="submit" class="btn btn-primary">Gửi</button>
    </div>
  </form>
</div>

<?php include("includes/footer.php"); ?>
<script>
  window.addEventListener('load', function () {
    var chatBox = document.querySelector('.chat-box');
    if (chatBox) {
      chatBox.scrollTop = chatBox.scrollHeight;
    }
  });
</script>
