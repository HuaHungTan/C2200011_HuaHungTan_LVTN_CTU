<?php include("includes/header.php"); ?>
<div class="container my-5" style="max-width: 500px;">
  <h4 class="text-primary">🔐 Quên mật khẩu</h4>
  <form method="post" action="xuly_forgotpassword.php" class="mt-3">
    <label class="form-label">Email đăng ký:</label>
    <input type="email" name="email" class="form-control" required>
    <div class="text-end mt-3">
      <button type="submit" class="btn btn-success">📩 Gửi yêu cầu</button>
    </div>
  </form>
</div>
<?php include("includes/footer.php"); ?>