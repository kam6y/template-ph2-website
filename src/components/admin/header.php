<header>
  <div class="p-header__logo"><img src="../../assets/img/logo.svg" alt="POSSE"></div>
  <?php if (isset($_SESSION['id'])) { ?>
  <div>
    <form method="POST" action="/admin/auth/signout.php">
      <input type="submit" value="ログアウト" class="submit"/>
    </form>
  </div>
  <?php } ?>
</header>

<style>
.submit {
  background-color: transparent;
  border: none;
}
</style>