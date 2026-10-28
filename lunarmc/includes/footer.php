<?php

require_once __DIR__ . '/../config.php';

?>

<hr>

<footer class="main-footer">

    <div id="footer-content">

        <p>
            <strong>
                <?php echo htmlspecialchars((string) DEVELOPER_NAME, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
            </strong>
        </p>

        <p>
            <strong>
                <?php echo htmlspecialchars((string) CURRENT_YEAR, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
            </strong>
        </p>

    </div>

</footer>


<?php
// index.php y rangos.php ya cargan este JS en el <head> con defer.
// Para las demás páginas se sigue cargando desde el footer.
$pagina_actual = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
if (!in_array($pagina_actual, ['index.php', 'rangos.php'], true)):
?>
<script src="index-.js?v=20261027-fix1" defer></script>
<?php endif; ?>


</body>

</html>