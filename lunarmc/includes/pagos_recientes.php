<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/database.php';

/* ==========================================================
   OBTENER PEDIDOS RECIENTES (PDO - TP7)
   ========================================================== */

$pagos_recientes = [];

try {
    $sql_pagos = "
        SELECT p.id, p.estado, u.nick
        FROM pedidos p
        INNER JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.estado IN ('pagado', 'entregado')
        ORDER BY p.id DESC
        LIMIT 12
    ";

    $stmt_pagos = $pdo->prepare($sql_pagos);
    $stmt_pagos->execute();
    $pagos_recientes = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('LunarMC - pagos recientes: ' . $e->getMessage());
    $pagos_recientes = [];
}

?>


<section class="recent-payments">


    <!-- ======================================================
         CABECERA
         ====================================================== -->

    <div class="recent-payments-header">


        <div class="recent-payments-icon">
            👥
        </div>


        <div>

            <h2 class="recent-payments-title">
                PAGOS RECIENTES
            </h2>


            <p class="recent-payments-subtitle">
                ¡Compra y aparecé acá!
            </p>

        </div>


    </div>



    <!-- ======================================================
         JUGADORES
         ====================================================== -->

    <div class="recent-payments-grid">


        <?php if (!empty($pagos_recientes)): ?>


            <?php foreach ($pagos_recientes as $pago): ?>


                <?php

                $nick_pago =
                    $pago['nick'] ?? 'Jugador';


                $skin_cabeza =
                    'https://mc-heads.net/avatar/' .
                    rawurlencode($nick_pago) .
                    '/48';

                ?>


                <div
                    class="recent-payment-user"
                    title="<?php

                    echo htmlspecialchars(
                        $nick_pago,
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    ?>"
                >


                    <img
                        src="<?php

                        echo htmlspecialchars(
                            $skin_cabeza,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>"
                        alt="Skin de <?php

                        echo htmlspecialchars(
                            $nick_pago,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>"
                        loading="lazy"
                    >


                </div>


            <?php endforeach; ?>


        <?php else: ?>


            <div class="recent-payment-empty">

                Todavía no hay compras confirmadas.

            </div>


        <?php endif; ?>


    </div>


</section>