<?php
session_start();
require('../database/conn.php');

$admin_id = 1;
$active_user_id = $_GET['user'] ?? null;

// ✅ Nếu đã chọn 1 user → đánh dấu tin nhắn đã đọc TRƯỚC khi truy vấn danh sách users
if ($active_user_id) {
  $conn->query("
    UPDATE messages 
    SET is_read = 1 
    WHERE sender_id = $active_user_id 
      AND receiver_id = $admin_id 
      AND is_read = 0
  ");
}

// 🔍 Truy vấn danh sách users đã từng trò chuyện
$users = $conn->query("
  SELECT u.user_id, u.name, u.avt,
         (
           SELECT MAX(sent_at) 
           FROM messages m 
           WHERE (m.sender_id = u.user_id AND m.receiver_id = $admin_id)
              OR (m.sender_id = $admin_id AND m.receiver_id = u.user_id)
         ) AS last_message_time,
         (
           SELECT sender_id 
           FROM messages m 
           WHERE ((m.sender_id = u.user_id AND m.receiver_id = $admin_id)
               OR (m.sender_id = $admin_id AND m.receiver_id = u.user_id))
           ORDER BY sent_at DESC LIMIT 1
         ) AS last_sender,
         (
           SELECT is_read 
           FROM messages m 
           WHERE ((m.sender_id = u.user_id AND m.receiver_id = $admin_id)
               OR (m.sender_id = $admin_id AND m.receiver_id = u.user_id))
           ORDER BY sent_at DESC LIMIT 1
         ) AS last_is_read
  FROM users u
  WHERE u.user_id != $admin_id
    AND EXISTS (
      SELECT 1 FROM messages 
      WHERE ((sender_id = u.user_id AND receiver_id = $admin_id)
         OR (sender_id = $admin_id AND receiver_id = u.user_id))
    )
  ORDER BY last_message_time DESC
");

if ($active_user_id) {
  $user_info = $conn->query("SELECT name, avt FROM users WHERE user_id = $active_user_id")->fetch_assoc();

  // 📩 Gửi tin nhắn
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (!empty($message)) {
      $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
      $stmt->bind_param("iis", $admin_id, $active_user_id, $message);
      $stmt->execute();
      $stmt->close();
      header("Location: cskh.php?user=$active_user_id");
      exit;
    }
  }

  // 🔄 Lấy tin nhắn giữa admin và user
  $messages = $conn->query("
    SELECT * FROM messages
    WHERE (sender_id = $admin_id AND receiver_id = $active_user_id)
       OR (sender_id = $active_user_id AND receiver_id = $admin_id)
    ORDER BY sent_at ASC
  ");
}

include("includes/header.php");
?>

<div class="container my-5">
  <h2 class="fw-bold text-primary mb-4">👥 CHĂM SÓC KHÁCH HÀNG</h2>
  <div class="row">

    <!-- 🧍‍♂️ Cột trái: Danh sách khách hàng -->
    <div class="col-md-4 border-end pe-3">
      <h5 class="mb-3">📂 Danh sách khách hàng đã nhắn tin</h5>
      <div class="list-group">
        <?php while ($u = $users->fetch_assoc()) {
          $active = ($u['user_id'] == $active_user_id) ? 'active' : '';
          $isUnread = ($u['last_sender'] == $u['user_id'] && $u['last_is_read'] == 0);

          echo "<a href='cskh.php?user={$u['user_id']}' class='list-group-item list-group-item-action d-flex align-items-center $active'>
                  <img src='../{$u['avt']}' class='rounded-circle me-2' style='width:32px;height:32px;object-fit:cover;'>
                  <span class='me-auto'>{$u['name']}</span>" .
                  ($isUnread ? "<span class='badge bg-danger rounded-pill ml-2'>●</span>" : "") .
                "</a>";
        } ?>
      </div>
    </div>

    <!-- 💬 Cột phải: Khung chat -->
    <div class="col-md-8">
      <?php if ($active_user_id): ?>
        <h5 class="mb-3">💬 Đang trò chuyện với: <strong><?= htmlspecialchars($user_info['name']) ?></strong></h5>
        <div class="chat-box border rounded p-3 mb-4" style="height:400px; overflow-y:auto; background:#f8f9fa;">
        <?php
          while ($row = $messages->fetch_assoc()) {
            $isAdmin = $row['sender_id'] == $admin_id;
            $alignClass = 'd-flex mb-3';
            $bubbleStyle = $isAdmin ? 'bg-primary text-white border-end' : 'bg-light text-dark border-start';
            $avatarUrl = $isAdmin
              ? ($_SESSION['avt'] ?? 'data_image/avatar/default.jpg')
              : $user_info['avt'];
            $nameLabel = $isAdmin ? 'Admin' : $user_info['name'];

            echo "<div class='$alignClass " . ($isAdmin ? 'justify-content-end text-end' : 'justify-content-start text-start') . "'>
                    " . ($isAdmin ? "
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

        <!-- ✍️ Form gửi tin nhắn -->
        <form method="post">
          <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Nhập tin nhắn..." required>
            <button type="submit" class="btn btn-primary">Gửi</button>
          </div>
        </form>
      <?php else: ?>
        <div class="alert alert-info">📌 Chọn khách hàng để tư vấn.</div>
      <?php endif; ?>
    </div>

  </div>
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
