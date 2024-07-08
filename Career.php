<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    ?>
    <script type="text/javascript">
        window.location="login.php";
    </script>
    <?php
}
?>

<?php
$mysqli = include "database.php";
include "headerwithouttimer.php";
?>

<!-- Centered text and button to navigate to another page -->
<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh;">
    <p style="margin-bottom: 20px; font-size: 24px;">You have to complete all the questions to Predict your career</p>
    <form action="old_quiz_results.php" method="GET">
        <button type="submit" style="background-color: blue; color: white; padding: 10px 20px; border: none; border-radius: 5px;">View My Results</button>
    </form>
</div>

<?php
include "footer.php";
?>