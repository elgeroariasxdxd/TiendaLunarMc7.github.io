// LunarMC: control único del tema desde el archivo JavaScript principal.
// Se ejecuta en captura para evitar que controladores antiguos dupliquen el clic.
(() => {
    'use strict';
    function aplicarPreferencia() {
        let tema = null;
        try { tema = localStorage.getItem('lunarmc-tema'); } catch (_) {}
        if (tema === 'claro') document.body.classList.remove('dark-mode');
        if (tema === 'oscuro') document.body.classList.add('dark-mode');
        const boton = document.getElementById('toggle-theme-btn');
        if (boton) boton.textContent = document.body.classList.contains('dark-mode') ? '☀️ Modo Claro' : '🌙 Modo Oscuro';
    }
    document.addEventListener('click', (evento) => {
        const boton = evento.target instanceof Element ? evento.target.closest('#toggle-theme-btn') : null;
        if (!boton) return;
        evento.preventDefault();
        evento.stopImmediatePropagation();
        document.body.classList.toggle('dark-mode');
        try { localStorage.setItem('lunarmc-tema', document.body.classList.contains('dark-mode') ? 'oscuro' : 'claro'); } catch (_) {}
        aplicarPreferencia();
    }, true);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', aplicarPreferencia, {once:true});
    } else {
        aplicarPreferencia();
    }
})();

// ==========================================================
// LunarMC - JavaScript principal
// ==========================================================


// ==========================================================
// 1. MENSAJE DE BIENVENIDA
// ==========================================================

window.addEventListener('DOMContentLoaded', function () {

    const mensajeBienvenida = "¡Bienvenido a la Tienda Oficial de LunarMC!";

    alert(mensajeBienvenida);

    console.log(mensajeBienvenida);

});


// ==========================================================
// 2. FECHA Y HORA EN TIEMPO REAL
// ==========================================================

function actualizarFechaHora() {

    const ahora = new Date();

    const dia = String(ahora.getDate()).padStart(2, '0');
    const mes = String(ahora.getMonth() + 1).padStart(2, '0');
    const anio = ahora.getFullYear();

    const fechaTexto = `${dia}/${mes}/${anio}`;

    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const segundos = String(ahora.getSeconds()).padStart(2, '0');

    const horaTexto = `${horas}:${minutos}:${segundos}`;

    const reloj = document.getElementById('clock');

    if (reloj) {

        reloj.textContent = horaTexto;

    } else {

        const contenedorFecha =
            document.getElementById('datetime-banner');

        if (contenedorFecha) {

            contenedorFecha.innerHTML =
                `Fecha: ${fechaTexto} | Hora actual: <span id="clock">${horaTexto}</span>`;

        }

    }

}

actualizarFechaHora();

setInterval(actualizarFechaHora, 1000);


// La cabecera administra copiar IP y el tema en todas las páginas.

// ==========================================================
// 4. ELEMENTOS INTERACTIVOS
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {
    // Tema y copiar IP: administrados por includes/header.php.



    // ======================================================
    // VALIDACIÓN DEL FORMULARIO
    // ======================================================

    const btnSubmit =
        document.getElementById('btn-submit-form');

    const btnConfirm =
        document.getElementById('btn-confirm-send');


    if (btnSubmit) {

        btnSubmit.addEventListener('click', function () {

            const usernameInput =
                document.getElementById('username-input');

            const emailInput =
                document.getElementById('email-input');


            const username =
                usernameInput
                    ? usernameInput.value.trim()
                    : "";

            const email =
                emailInput
                    ? emailInput.value.trim()
                    : "";


            const usernameError =
                document.getElementById('username-error');

            const emailError =
                document.getElementById('email-error');


            let esValido = true;


            if (usernameError) {

                usernameError.textContent = "";

            }

            if (emailError) {

                emailError.textContent = "";

            }


            if (username === "") {

                if (usernameError) {

                    usernameError.textContent =
                        "El nombre de usuario no puede estar vacío.";

                }

                esValido = false;

            }


            const regexEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (email === "") {

                if (emailError) {

                    emailError.textContent =
                        "El correo electrónico no puede estar vacío.";

                }

                esValido = false;

            } else if (!regexEmail.test(email)) {

                if (emailError) {

                    emailError.textContent =
                        "Por favor, ingresa un correo electrónico válido.";

                }

                esValido = false;

            }


            if (esValido) {

                const summaryUser =
                    document.getElementById('summary-username');

                const summaryMail =
                    document.getElementById('summary-email');

                const formSummary =
                    document.getElementById('form-summary');


                if (summaryUser) {

                    summaryUser.textContent =
                        "Usuario: " + username;

                }

                if (summaryMail) {

                    summaryMail.textContent =
                        "Correo: " + email;

                }

                if (formSummary) {

                    formSummary.style.display = "block";

                }

            }

        });

    }


    if (btnConfirm) {

        btnConfirm.addEventListener('click', function () {

            alert("¡Formulario enviado con éxito!");

            const formSummary =
                document.getElementById('form-summary');

            const userForm =
                document.getElementById('user-form');


            if (formSummary) {

                formSummary.style.display = "none";

            }

            if (userForm) {

                userForm.reset();

            }

        });

    }


    // ======================================================
    // ACORDEÓN
    // ======================================================

    const accordionBtns =
        document.querySelectorAll('.accordion-btn');


    accordionBtns.forEach(function (btn) {

        btn.addEventListener('click', function () {

            const content =
                this.nextElementSibling;

            if (content) {

                content.style.display =
                    content.style.display === "block"
                        ? "none"
                        : "block";

            }

        });

    });


    // ======================================================
    // GALERÍA
    // ======================================================

    const galleryImages = [

        "imagen del servidor lunar.png",

        "miniatura2.png",

        "miniatura3.png"

    ];


    let currentImgIndex = 0;


    const galleryDisplay =
        document.getElementById('gallery-display');

    const btnPrev =
        document.getElementById('btn-prev-img');

    const btnNext =
        document.getElementById('btn-next-img');

    const thumbnails =
        document.querySelectorAll(
            '.gallery-thumbnails .thumb'
        );


    function updateGallery(index) {

        currentImgIndex = index;


        if (galleryDisplay) {

            galleryDisplay.src =
                galleryImages[currentImgIndex];

        }


        thumbnails.forEach(function (thumb, i) {

            thumb.style.borderColor =
                i === currentImgIndex
                    ? "#7289DA"
                    : "#2D323F";

        });

    }


    if (btnNext && btnPrev) {

        btnNext.onclick = function (e) {

            e.preventDefault();

            currentImgIndex =
                (currentImgIndex + 1)
                % galleryImages.length;

            updateGallery(currentImgIndex);

        };


        btnPrev.onclick = function (e) {

            e.preventDefault();

            currentImgIndex =
                (currentImgIndex - 1 + galleryImages.length)
                % galleryImages.length;

            updateGallery(currentImgIndex);

        };

    }


    thumbnails.forEach(function (thumb, index) {

        thumb.addEventListener('click', function () {

            updateGallery(index);

        });

    });


    // ======================================================
    // INFORMACIÓN DE LOS RANGOS
    // ======================================================

    const datosRangos = {

        sun: {

            titulo: "SUN",

            imagen: "sun rango.png",

            precioOld: "9.97",

            precio: "2.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo SUN en tu nombre del Chat.</li>

                        <li>👑 Prefijo SUN en tu nombre del Tabulador.</li>

                        <li>👑 Rol SUN en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 1 mochila virtual.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas de rango sun.png"
                            alt="Recompensas del rango SUN"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo SUN en Chat.</li>

                        <li>👑 Prefijo SUN en Tabulador.</li>

                        <li>👑 Rol SUN en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit SUN</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp SUN</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp SUN</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina sun.png"
                            alt="Mina del rango SUN"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        },


        venus: {

            titulo: "VENUS",

            imagen: "rango venus.png",

            precioOld: "19.97",

            precio: "5.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo VENUS en tu nombre del Chat.</li>

                        <li>👑 Prefijo VENUS en tu nombre del Tabulador.</li>

                        <li>👑 Rol VENUS en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 2 mochilas virtuales.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas de rango venus.png"
                            alt="Recompensas del rango VENUS"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo VENUS en Chat.</li>

                        <li>👑 Prefijo VENUS en Tabulador.</li>

                        <li>👑 Rol VENUS en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit VENUS</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp VENUS</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp VENUS</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina venus.png"
                            alt="Mina del rango VENUS"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        },


        venus_plus: {

            titulo: "VENUS+",

            imagen: "rango venus+.png",

            precioOld: "33.30",

            precio: "9.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo VENUS+ en tu nombre del Chat.</li>

                        <li>👑 Prefijo VENUS+ en tu nombre del Tabulador.</li>

                        <li>👑 Rol VENUS+ en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 3 mochilas virtuales.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas rango venus plus.png"
                            alt="Recompensas del rango VENUS+"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo VENUS+ en Chat.</li>

                        <li>👑 Prefijo VENUS+ en Tabulador.</li>

                        <li>👑 Rol VENUS+ en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit VENUS+</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp VENUS+</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp VENUS+</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina venus +.png"
                            alt="Mina del rango VENUS+"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        },


        nova: {

            titulo: "NOVA",

            imagen: "rango nova.png",

            precioOld: "49.97",

            precio: "14.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo NOVA en tu nombre del Chat.</li>

                        <li>👑 Prefijo NOVA en tu nombre del Tabulador.</li>

                        <li>👑 Rol NOVA en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 3 mochilas virtuales.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas nova.png"
                            alt="Recompensas del rango NOVA"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo NOVA en Chat.</li>

                        <li>👑 Prefijo NOVA en Tabulador.</li>

                        <li>👑 Rol NOVA en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit NOVA</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp NOVA</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp NOVA</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina nova.png"
                            alt="Mina del rango NOVA"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        },


        lunar: {

            titulo: "LUNAR",

            imagen: "rango lunar.png",

            precioOld: "56.63",

            precio: "16.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo LUNAR en tu nombre del Chat.</li>

                        <li>👑 Prefijo LUNAR en tu nombre del Tabulador.</li>

                        <li>👑 Rol LUNAR en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 3 mochilas virtuales.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas lunar.png"
                            alt="Recompensas del rango LUNAR"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo LUNAR en Chat.</li>

                        <li>👑 Prefijo LUNAR en Tabulador.</li>

                        <li>👑 Rol LUNAR en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit LUNAR</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp LUNAR</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp LUNAR</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina lunar.png"
                            alt="Mina del rango LUNAR"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        },


        summer: {

            titulo: "SUMMER",

            imagen: "rango summer.png",

            precioOld: "69.97",

            precio: "20.99",

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        RANGO PERMANENTE
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo SUMMER en tu nombre del Chat.</li>

                        <li>👑 Prefijo SUMMER en tu nombre del Tabulador.</li>

                        <li>👑 Rol SUMMER en nuestro Discord.</li>

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>💎 Acceso a una mina VIP exclusiva.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        QUE GANAS
                    </div>

                    <ul class="rank-info-list">

                        <li>💎 Recompensas diarias de tu rango.</li>

                        <li>💎 Kit especial de tu rango.</li>

                        <li>✅ Todos los Kill Effects.</li>

                        <li>⚡ Acceso a 3 mochilas virtuales.</li>

                    </ul>


                    <div class="rank-media-box">

                        <img
                            src="recompensas summer.png"
                            alt="Recompensas del rango SUMMER"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        ESTATUS
                    </div>

                    <ul class="rank-info-list">

                        <li>👑 Prefijo SUMMER en Chat.</li>

                        <li>👑 Prefijo SUMMER en Tabulador.</li>

                        <li>👑 Rol SUMMER en Discord.</li>

                        <li>⚡ Prioridad en la cola.</li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            ⚡ <code>/feed</code> para saciar el hambre.
                        </li>

                        <li>
                            ⚡ <code>/near</code> para ver jugadores cercanos.
                        </li>

                        <li>
                            ⚡ <code>/ec</code> para abrir el EnderChest remotamente.
                        </li>

                        <li>
                            ⭐ <code>/kit SUMMER</code> para reclamar el kit exclusivo.
                        </li>

                        <li>
                            ⭐ <code>/warp SUMMER</code> para acceder a la mina exclusiva.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        MINA EXCLUSIVA
                    </div>

                    <p class="rank-info-text">

                        ⭐ Acceso a la mina exclusiva de tu rango:
                        <code>/warp SUMMER</code>

                    </p>

                    <div class="rank-media-box">

                        <img
                            src="mina summer.png"
                            alt="Mina del rango SUMMER"
                            class="rank-detail-image"
                        >

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto,
                        automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `

        }

    };


    // ======================================================
    // ABRIR MODAL DE RANGO
    // ======================================================

    window.abrirModalRango = function (idRango) {

    const info =
        datosRangos[idRango];

    const modal =
        document.getElementById('info-modal');

    const imgElem =
        document.getElementById('modal-img');

    const titleElem =
        document.getElementById('modal-title');

    const oldPriceElem =
        document.getElementById('modal-price-old');

    const priceElem =
        document.getElementById('modal-price');

    const contentElem =
        document.getElementById('modal-details-content');

    const botonAgregarModal =
        document.querySelector(
            '#info-modal .btn-add-cart-large'
        );


    if (!info || !modal) {

        console.error(
            'LunarMC: no existe el rango o el modal:',
            idRango
        );

        return;
    }


    // IMPORTANTE:
    // El botón grande del modal recibe el ID real
    // del producto que se está mostrando.

    if (botonAgregarModal) {

        botonAgregarModal.dataset.product =
            idRango;

    }


    if (imgElem) {

        imgElem.src =
            info.imagen;

        imgElem.alt =
            'Rango ' + info.titulo;

    }


    if (titleElem) {

        titleElem.textContent =
            info.titulo;

    }


    if (oldPriceElem) {

        oldPriceElem.textContent =
            info.precioOld;

    }


    if (priceElem) {

        const precioMysql = window.LUNARMC_PRECIOS?.[idRango];
        priceElem.textContent = precioMysql
            ? (precioMysql.activo === 1 ? precioMysql.precio : 'No disponible')
            : info.precio;

    }


    if (contentElem) {

        contentElem.innerHTML =
            info.contenidoHTML;

    }


    modal.classList.add('active');

    modal.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.style.overflow =
        'hidden';

};


    // ======================================================
    // CERRAR MODAL
    // ======================================================

    window.cerrarModalRango = function () {

        const modal =
            document.getElementById('info-modal');


        if (modal) {

            modal.classList.remove('active');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow =
                '';

        }

    };


    // ======================================================
    // BOTONES DE INFORMACIÓN
    // ======================================================

    const botonesInfo =
        document.querySelectorAll('.btn-info');


    botonesInfo.forEach(function (boton) {

        boton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                const idRango =
                    boton.getAttribute(
                        'data-rango'
                    );


                if (idRango) {

                    window.abrirModalRango(
                        idRango
                    );

                }

            }
        );

    });


    // ======================================================
    // BOTÓN CERRAR
    // ======================================================

    const botonCerrarModal =
        document.getElementById(
            'btn-close-modal'
        );


    if (botonCerrarModal) {

        botonCerrarModal.addEventListener(
            'click',
            function () {

                window.cerrarModalRango();

            }
        );

    }


    // ======================================================
    // CERRAR HACIENDO CLICK FUERA
    // ======================================================

    const modal =
        document.getElementById(
            'info-modal'
        );


    if (modal) {

        modal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === modal
                ) {

                    window.cerrarModalRango();

                }

            }
        );

    }

});


// ==========================================================
// CERRAR MODAL CON ESC
// ==========================================================

window.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key === 'Escape' &&
            typeof window.cerrarModalRango === 'function'
        ) {

            window.cerrarModalRango();

        }

    }
);
// ==========================================================
// EXTRAS - INFORMACIÓN EN MODAL
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {

    const datosExtras = {

        // ==================================================
        // TAGS
        // ==================================================
        tags: {

            titulo: 'TAGS',

            imagen: 'imagen de tags.png',

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        INFORMACIÓN
                    </div>

                    <p class="rank-info-text">
                        🚀 ¡Te presentamos los TAGS de LUNARMC Network!
                    </p>

                    <ul class="rank-info-list">

                        <li>
                            💎 Obtendrás todos los TAGS disponibles.
                        </li>

                        <li>
                            ✅ Usa el comando
                            <code>/tag</code>
                            para ver todos los tags.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        TAGS LUNARMC
                    </div>

                    <div class="extra-media-grid">

                        <div class="rank-media-box">

                            <img
                                src="tags de lunarmc 2.png"
                                alt="Tags de LunarMC"
                                class="rank-detail-image"
                            >

                        </div>


                        <div class="rank-media-box">

                            <img
                                src="tags de lunar mc.png"
                                alt="Tags de LunarMC"
                                class="rank-detail-image"
                            >

                        </div>

                    </div>

                </section>


                <section class="rank-info-section terms-section">

                    <div class="rank-pixel-heading">
                        TÉRMINOS Y CONDICIONES
                    </div>

                    <p class="rank-info-text">
                        Al comprar este producto, automáticamente aceptas lo siguiente:
                    </p>

                    <ul class="rank-info-list warning-list">

                        <li>
                            ⚠️ Confirmas haber leído los términos y condiciones.
                        </li>

                        <li>
                            ⚠️ Entiendes que no se aceptarán reembolsos
                            bajo ninguna circunstancia.
                        </li>

                        <li>
                            ⚠️ Reconoces que el producto es personal e intransferible.
                        </li>

                        <li>
                            ⚠️ Certificas haber revisado todas las funciones
                            del producto y que cumplen tus expectativas.
                        </li>

                    </ul>

                </section>

            `
        },


        // ==================================================
        // KILL EFFECTS
        // ==================================================
        kill_effects: {

            titulo: 'KILL EFFECTS',

            imagen: 'imagen de kill efects.png',

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        INFORMACIÓN
                    </div>

                    <p class="rank-info-text">
                        🚀 ¡Te presentamos los Kill Effects de LUNARMC Network!
                    </p>

                    <ul class="rank-info-list">

                        <li>
                            💎 Obtendrás todos los KILL EFFECTS.
                        </li>

                        <li>
                            ✅ Usa el comando
                            <code>/killeffects</code>
                            para ver la lista completa.
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        COMANDOS
                    </div>

                    <ul class="rank-info-list extra-command-list">

                        <li>
                            <code>/betterkill set totem</code>
                            — Aparecerá un totem en tu pantalla.
                        </li>

                        <li>
                            <code>/betterkill set totem_particle</code>
                            — Aparecerán las partículas del totem.
                        </li>

                        <li>
                            <code>/betterkill set ray</code>
                            — Aparecerá un rayo en tu pantalla.
                        </li>

                        <li>
                            <code>/betterkill set angel</code>
                            — Aparecerá una corona de ángel en tu cabeza.
                        </li>

                        <li>
                            <code>/betterkill set demon</code>
                            — Aparecerá una corona de demonio en tu cabeza.
                        </li>

                    </ul>

                </section>

            `
        },


        // ==================================================
        // LLAVE TODO O NADA
        // ==================================================
        llave_todo_o_nada: {

            titulo: 'LLAVE TODO O NADA',

            imagen: 'llave todo o nada.png',

            contenidoHTML: `

                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        INFORMACIÓN
                    </div>

                    <p class="rank-info-text">
                        🔑 Al comprar este paquete recibirás 1 llave TODO O NADA
                        que podrás canjear en la crate todo o nada.
                    </p>

                    <p class="rank-info-text">
                        ⚠️ Recuerda que debes tener al menos 1 espacio libre
                        en el inventario para poder recibir esta llave.
                    </p>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        PROBABILIDADES
                    </div>

                    <ul class="rank-info-list">

                        <li>
                            🏆 Rango Summer:
                            <strong>50%</strong>
                        </li>

                        <li>
                            ❌ Nada:
                            <strong>50%</strong>
                        </li>

                    </ul>

                </section>


                <section class="rank-info-section">

                    <div class="rank-pixel-heading">
                        DETALLES
                    </div>

                    <div class="extra-media-grid">

                        <div class="rank-media-box">

                            <img
                                src="llave todo o nada.png"
                                alt="Llave Todo o Nada"
                                class="rank-detail-image"
                            >

                        </div>


                        <div class="rank-media-box">

                            <img
                                src="llave todo o nada 50.png"
                                alt="imagen de la probabilidad de la llave todo o nada"
                                class="rank-detail-image"
                            >

                        </div>

                    </div>

                </section>

            `
        }

    };


    // ======================================================
    // ABRIR MODAL DE EXTRA
    // ======================================================

    window.abrirModalExtra = function (idExtra) {

    const info =
        datosExtras[idExtra];

    const modal =
        document.getElementById('info-modal');

    const imgElem =
        document.getElementById('modal-img');

    const titleElem =
        document.getElementById('modal-title');

    const oldPriceElem =
        document.getElementById('modal-price-old');

    const priceElem =
        document.getElementById('modal-price');

    const pricingElem =
        document.querySelector(
            '#info-modal .modal-pricing'
        );

    const contentElem =
        document.getElementById(
            'modal-details-content'
        );

    const botonAgregarModal =
        document.querySelector(
            '#info-modal .btn-add-cart-large'
        );


    if (!info || !modal) {

        console.error(
            'LunarMC: no existe información para el extra:',
            idExtra
        );

        return;
    }


    // IMPORTANTE:
    // Mandamos el ID verdadero del extra.

    if (botonAgregarModal) {

        botonAgregarModal.dataset.product =
            idExtra;

    }


    if (imgElem) {

        imgElem.src =
            info.imagen;

        imgElem.alt =
            'Extra ' + info.titulo;

    }


    if (titleElem) {

        titleElem.textContent =
            info.titulo;

    }


    if (oldPriceElem) {

        oldPriceElem.textContent =
            '';

    }


    if (priceElem) {

        priceElem.textContent =
            '';

    }


    if (pricingElem) {

        pricingElem.style.display =
            'none';

    }


    if (contentElem) {

        contentElem.innerHTML =
            info.contenidoHTML;

    }


    modal.classList.add('active');

    modal.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.style.overflow =
        'hidden';

};


    // ======================================================
    // BOTONES ⓘ DE EXTRAS
    // ======================================================

    const botonesExtra =
        document.querySelectorAll('.btn-extra-info');


    botonesExtra.forEach(function (boton) {

        boton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();

                event.stopPropagation();


                const idExtra =
                    boton.getAttribute(
                        'data-extra'
                    );


                if (idExtra) {

                    window.abrirModalExtra(
                        idExtra
                    );

                }

            }
        );

    });


    // ======================================================
    // CERRAR MODAL
    // ======================================================

    const modalExtra =
        document.getElementById('info-modal');

    const botonCerrarExtra =
        document.getElementById('btn-close-modal');


    if (botonCerrarExtra) {

        botonCerrarExtra.addEventListener(
            'click',
            function () {

                if (
                    typeof window.cerrarModalRango ===
                    'function'
                ) {

                    window.cerrarModalRango();

                } else if (modalExtra) {

                    modalExtra.classList.remove(
                        'active'
                    );

                    modalExtra.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    document.body.style.overflow =
                        '';

                }

            }
        );

    }


    // ======================================================
    // CERRAR HACIENDO CLICK FUERA
    // ======================================================

    if (modalExtra) {

        modalExtra.addEventListener(
            'click',
            function (event) {

                if (
                    event.target === modalExtra
                ) {

                    if (
                        typeof window.cerrarModalRango ===
                        'function'
                    ) {

                        window.cerrarModalRango();

                    } else {

                        modalExtra.classList.remove(
                            'active'
                        );

                        modalExtra.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                        document.body.style.overflow =
                            '';

                    }

                }

            }
        );

    }

});
/* ==========================================================
   MENÚ DE CUENTA
   ========================================================== */

document.addEventListener('DOMContentLoaded', function () {

    const openAccountButton = document.getElementById('open-account-menu');
    const accountOverlay = document.getElementById('account-menu-overlay');
    const closeAccountButton = document.getElementById('close-account-menu');
    const currencySelector = document.getElementById('currency-selector');
    const cartTotal = document.getElementById('account-cart-total');

    // En páginas donde no existe el panel de cuenta, no hacemos nada.
    if (!openAccountButton || !accountOverlay) {
        return;
    }


    /* ======================================================
       ABRIR MENÚ
       ====================================================== */

    function abrirMenuCuenta() {

        accountOverlay.classList.add('active');

        accountOverlay.setAttribute(
            'aria-hidden',
            'false'
        );

        openAccountButton.setAttribute(
            'aria-expanded',
            'true'
        );

    }


    /* ======================================================
       CERRAR MENÚ
       ====================================================== */

    function cerrarMenuCuenta() {

        accountOverlay.classList.remove('active');

        accountOverlay.setAttribute(
            'aria-hidden',
            'true'
        );

        openAccountButton.setAttribute(
            'aria-expanded',
            'false'
        );

    }


    /* ======================================================
       ABRIR AUTOMÁTICAMENTE CON ?cuenta=1
       ====================================================== */

    const parametros = new URLSearchParams(
        window.location.search
    );

    if (parametros.get('cuenta') === '1') {

        abrirMenuCuenta();

        /*
         * Quitamos ?cuenta=1 después de abrir el menú
         * para dejar limpia la URL.
         */

        parametros.delete('cuenta');

        const nuevaQuery = parametros.toString();

        const nuevaURL =
            window.location.pathname +
            (nuevaQuery ? '?' + nuevaQuery : '') +
            window.location.hash;

        window.history.replaceState(
            {},
            '',
            nuevaURL
        );

    }


    /* ======================================================
       CLICK EN "MI CUENTA"
       ====================================================== */

    openAccountButton.addEventListener(
        'click',
        function (event) {

            event.preventDefault();
            event.stopPropagation();

            abrirMenuCuenta();

        }
    );


    /* ======================================================
       BOTÓN X PARA CERRAR
       ====================================================== */

    if (closeAccountButton) {

        closeAccountButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();
                event.stopPropagation();

                cerrarMenuCuenta();

            }
        );

    }


    /* ======================================================
       CERRAR HACIENDO CLICK FUERA DEL PANEL
       ====================================================== */

    accountOverlay.addEventListener(
        'click',
        function (event) {

            if (event.target === accountOverlay) {

                cerrarMenuCuenta();

            }

        }
    );


    /* ======================================================
       CERRAR CON ESC
       ====================================================== */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                accountOverlay.classList.contains('active')
            ) {

                cerrarMenuCuenta();

            }

        }
    );


    /* ======================================================
       MONEDA GUARDADA
       ====================================================== */

    if (currencySelector) {

        const monedaGuardada =
            localStorage.getItem('lunarmc_currency');

        if (
            monedaGuardada === 'USD' ||
            monedaGuardada === 'ARS' ||
            monedaGuardada === 'EUR'
        ) {

            currencySelector.value =
                monedaGuardada;

        }


        currencySelector.addEventListener(
            'change',
            function () {

                localStorage.setItem(
                    'lunarmc_currency',
                    currencySelector.value
                );

                actualizarMoneda();

            }
        );

    }


    /* ======================================================
       ACTUALIZAR TOTAL DEL CARRITO
       ====================================================== */

    function actualizarMoneda() {

        if (!currencySelector || !cartTotal) {
            return;
        }


        const usd = parseFloat(
            cartTotal.dataset.usd || '0'
        );


        const moneda =
            currencySelector.value || 'USD';


        /*
         * Tasas de ejemplo.
         *
         * Si después tenés una API de monedas,
         * podemos reemplazar esto por valores reales.
         */

        const tasas = {
            USD: 1,
            ARS: 1000,
            EUR: 0.85
        };


        const simbolos = {
            USD: '$',
            ARS: '$',
            EUR: '€'
        };


        const tasa =
            tasas[moneda] ?? 1;


        const simbolo =
            simbolos[moneda] ?? '$';


        const convertido =
            usd * tasa;


        cartTotal.textContent =
            simbolo +
            convertido.toLocaleString(
                'es-AR',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            ) +
            ' ' +
            moneda;

    }


    /* ======================================================
       ACTUALIZAR AL CARGAR
       ====================================================== */

    actualizarMoneda();

});
// ==========================================================
// CARRITO - LUNARMC
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {

    // ======================================================
    // CONFIRMACIONES DEL CHECKOUT
    // ======================================================

    const confirmAccount =
        document.getElementById('confirm-account');

    const confirmTerms =
        document.getElementById('confirm-terms');

    const checkoutButton =
        document.getElementById('checkout-button');


    function actualizarCheckout() {

        if (
            !confirmAccount ||
            !confirmTerms ||
            !checkoutButton
        ) {
            return;
        }

        const permitido =
            confirmAccount.checked &&
            confirmTerms.checked;

        if (permitido) {

            checkoutButton.classList.remove(
                'disabled'
            );

        } else {

            checkoutButton.classList.add(
                'disabled'
            );

        }

    }


    if (confirmAccount) {

        confirmAccount.addEventListener(
            'change',
            actualizarCheckout
        );

    }


    if (confirmTerms) {

        confirmTerms.addEventListener(
            'change',
            actualizarCheckout
        );

    }


    actualizarCheckout();


    // ======================================================
    // EVITAR CONTINUAR SI NO CONFIRMÓ
    // ======================================================

    if (checkoutButton) {

        checkoutButton.addEventListener(
            'click',
            function (event) {

                if (
                    checkoutButton.classList.contains(
                        'disabled'
                    )
                ) {

                    event.preventDefault();

                }

            }
        );

    }


    // ======================================================
    // CUPÓN
    // ======================================================

    const couponButton =
        document.getElementById('apply-coupon');

    const couponInput =
        document.getElementById('coupon-code');

    const couponMessage =
        document.getElementById('coupon-message');


    if (
        couponButton &&
        couponInput &&
        couponMessage
    ) {

        couponButton.addEventListener(
            'click',
            function () {

                const codigo =
                    couponInput.value.trim();


                if (codigo === '') {

                    couponMessage.textContent =
                        'Ingresá un código primero.';

                    couponMessage.classList.remove(
                        'coupon-success'
                    );

                    couponMessage.classList.add(
                        'coupon-error'
                    );

                    return;

                }


                couponMessage.textContent =
                    'Los cupones todavía no están habilitados.';

                couponMessage.classList.remove(
                    'coupon-error'
                );

                couponMessage.classList.add(
                    'coupon-error'
                );

            }
        );


        couponInput.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    couponButton.click();

                }

            }
        );

    }

});


// ==========================================================
// CAMBIAR CANTIDAD DEL CARRITO
// ==========================================================

function cambiarCantidad(producto, cambio) {

    if (!producto) {
        return;
    }


    const cambioNumero =
        parseInt(cambio, 10);


    if (
        cambioNumero !== 1 &&
        cambioNumero !== -1
    ) {
        return;
    }


    // ======================================================
    // CREAR FORMULARIO POST
    // ======================================================

    const form =
        document.createElement('form');

    form.method =
        'POST';

    form.action =
        'carrito.php';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = window.lunarmcCsrfToken || '';
    form.appendChild(csrfInput);


    // ======================================================
    // PRODUCTO
    // ======================================================

    const productoInput =
        document.createElement('input');

    productoInput.type =
        'hidden';

    productoInput.name =
        'producto';

    productoInput.value =
        producto;


    // ======================================================
    // CAMBIO DE CANTIDAD
    // ======================================================

    const cambioInput =
        document.createElement('input');

    cambioInput.type =
        'hidden';

    cambioInput.name =
        'cambio';

    cambioInput.value =
        cambioNumero;


    // ======================================================
    // AGREGAR CAMPOS
    // ======================================================

    form.appendChild(
        productoInput
    );

    form.appendChild(
        cambioInput
    );


    // ======================================================
    // ENVIAR AL CARRITO
    // ======================================================

    document.body.appendChild(
        form
    );

    form.submit();

}


// ==========================================================
// DISPONIBLE PARA onclick="" DE carrito.php
// ==========================================================

window.cambiarCantidad =
    cambiarCantidad;
    // ==========================================================
// MINI-CARRITO LATERAL - LUNARMC
// ==========================================================

document.addEventListener('DOMContentLoaded', function () {

    crearMiniCarrito();


    document.addEventListener('click', function (event) {

        const botonAgregar =
            event.target.closest(
                '.btn-add-cart, .btn-add-cart-large'
            );

        if (!botonAgregar) {
            return;
        }


        const producto =
            botonAgregar.dataset.product || '';


        if (!producto) {

            console.error(
                'LunarMC: el botón no tiene data-product.'
            );

            return;
        }


        event.preventDefault();


        agregarProductoMiniCarrito(
            producto,
            botonAgregar
        );

    });


    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            cerrarMiniCarrito();
        }

    });

});


// ==========================================================
// CREAR HTML DEL MINI-CARRITO
// ==========================================================

function crearMiniCarrito() {

    if (
        document.getElementById(
            'lunarmc-mini-cart-overlay'
        )
    ) {
        return;
    }


    const overlay =
        document.createElement('div');

    overlay.id =
        'lunarmc-mini-cart-overlay';

    overlay.className =
        'lunarmc-mini-cart-overlay';


    overlay.innerHTML = `

        <aside
            class="lunarmc-mini-cart"
            id="lunarmc-mini-cart"
            aria-hidden="true"
        >

            <div class="lunarmc-mini-cart-header">

                <div>

                    <span class="lunarmc-mini-cart-label">
                        LUNARMC STORE
                    </span>

                    <h2>
                        Tu carrito
                    </h2>

                </div>


                <button
                    type="button"
                    class="lunarmc-mini-cart-close"
                    id="lunarmc-mini-cart-close"
                    aria-label="Cerrar carrito"
                >
                    ×
                </button>

            </div>


            <div
                class="lunarmc-mini-cart-items"
                id="lunarmc-mini-cart-items"
            >

                <div class="lunarmc-mini-cart-loading">
                    Agregando producto...
                </div>

            </div>


            <div class="lunarmc-mini-cart-footer">

                <div class="lunarmc-mini-cart-total">

                    <span>
                        Subtotal
                    </span>

                    <strong
                        id="lunarmc-mini-cart-total"
                    >
                        $0.00 USD
                    </strong>

                </div>


                <a
                    href="carrito.php"
                    class="lunarmc-mini-cart-view"
                >
                    VER CARRITO
                </a>


                <button
                    type="button"
                    class="lunarmc-mini-cart-continue"
                    id="lunarmc-mini-cart-continue"
                >
                    SEGUIR COMPRANDO
                </button>

            </div>

        </aside>

    `;


    document.body.appendChild(
        overlay
    );


    const cerrar =
        document.getElementById(
            'lunarmc-mini-cart-close'
        );

    const continuar =
        document.getElementById(
            'lunarmc-mini-cart-continue'
        );


    if (cerrar) {

        cerrar.addEventListener(
            'click',
            cerrarMiniCarrito
        );

    }


    if (continuar) {

        continuar.addEventListener(
            'click',
            cerrarMiniCarrito
        );

    }


    overlay.addEventListener(
        'click',
        function (event) {

            if (event.target === overlay) {
                cerrarMiniCarrito();
            }

        }
    );

}


// ==========================================================
// AGREGAR PRODUCTO
// ==========================================================

function agregarProductoMiniCarrito(
    producto,
    boton
) {

    const textoOriginal =
        boton.innerHTML;


    const campoCsrf =
        document.getElementById(
            'lunarmc-csrf-token'
        );


    const csrfToken =
        campoCsrf
            ? campoCsrf.value
            : '';


    if (!csrfToken) {

        console.error(
            'LunarMC: no se encontró el token CSRF.'
        );

        return;

    }


    boton.classList.add(
        'is-loading'
    );


    boton.textContent =
        'Agregando...';


    const datos =
        new URLSearchParams();


    datos.set(
        'producto',
        producto
    );


    datos.set(
        'csrf_token',
        csrfToken
    );


    datos.set(
        'ajax',
        '1'
    );


    fetch(
        'agregar carrito.php',
        {

            method: 'POST',

            credentials:
                'same-origin',

            headers: {

                'Content-Type':
                    'application/x-www-form-urlencoded; charset=UTF-8',

                'X-Requested-With':
                    'XMLHttpRequest'

            },

            body:
                datos.toString()

        }
    )

        .then(function (respuesta) {

            if (!respuesta.ok) {

                throw new Error(
                    'Error HTTP ' +
                    respuesta.status
                );

            }

            return respuesta.json();

        })

        .then(function (datosRespuesta) {

            if (
                !datosRespuesta ||
                datosRespuesta.ok !== true
            ) {

                throw new Error(
                    datosRespuesta &&
                    datosRespuesta.mensaje
                        ? datosRespuesta.mensaje
                        : 'No se pudo agregar.'
                );

            }


            renderizarMiniCarrito(
                datosRespuesta
            );


            abrirMiniCarrito();


            boton.innerHTML =
                '✓ Agregado';


            setTimeout(
                function () {

                    boton.innerHTML =
                        textoOriginal;

                    boton.classList.remove(
                        'is-loading'
                    );

                },
                900
            );

        })

        .catch(function (error) {

            console.error(
                'Mini-carrito LunarMC:',
                error
            );


            boton.innerHTML =
                textoOriginal;


            boton.classList.remove(
                'is-loading'
            );


            /*
             * Respaldo:
             * si AJAX falla, hacemos un POST normal.
             */

            const formulario =
                document.createElement(
                    'form'
                );


            formulario.method =
                'POST';

            formulario.action =
                'agregar carrito.php';

            formulario.style.display =
                'none';


            const campoProducto =
                document.createElement(
                    'input'
                );


            campoProducto.type =
                'hidden';

            campoProducto.name =
                'producto';

            campoProducto.value =
                producto;


            const campoToken =
                document.createElement(
                    'input'
                );


            campoToken.type =
                'hidden';

            campoToken.name =
                'csrf_token';

            campoToken.value =
                csrfToken;


            formulario.appendChild(
                campoProducto
            );


            formulario.appendChild(
                campoToken
            );


            document.body.appendChild(
                formulario
            );


            formulario.submit();

        });

}


// ==========================================================
// RENDERIZAR PRODUCTOS
// ==========================================================

function renderizarMiniCarrito(datos) {

    const contenedor =
        document.getElementById(
            'lunarmc-mini-cart-items'
        );

    const total =
        document.getElementById(
            'lunarmc-mini-cart-total'
        );


    if (
        !contenedor ||
        !total
    ) {
        return;
    }


    const items =
        Array.isArray(datos.items)
            ? datos.items
            : [];


    if (items.length === 0) {

        contenedor.innerHTML = `

            <div class="lunarmc-mini-cart-empty">
                Tu carrito está vacío.
            </div>

        `;

    } else {

        contenedor.innerHTML =
            items
                .map(function (item) {

                    const nombre =
                        escaparMiniCarrito(
                            item.nombre
                        );

                    const imagen =
                        escaparMiniCarrito(
                            item.imagen
                        );

                    const cantidad =
                        parseInt(
                            item.cantidad,
                            10
                        ) || 1;

                    const subtotal =
                        Number(
                            item.subtotal
                        ) || 0;


                    return `

                        <article
                            class="lunarmc-mini-cart-item"
                        >

                            <div
                                class="lunarmc-mini-cart-image"
                            >

                                ${
                                    imagen
                                        ? `
                                            <img
                                                src="${imagen}"
                                                alt="${nombre}"
                                            >
                                        `
                                        : `
                                            <span>
                                                📦
                                            </span>
                                        `
                                }

                            </div>


                            <div
                                class="lunarmc-mini-cart-info"
                            >

                                <strong>
                                    ${nombre}
                                </strong>

                                <span>
                                    Cantidad:
                                    ${cantidad}
                                </span>

                            </div>


                            <div
                                class="lunarmc-mini-cart-price"
                            >

                                $${subtotal.toFixed(2)}

                                <small>
                                    USD
                                </small>

                            </div>

                        </article>

                    `;

                })
                .join('');

    }


    total.textContent =
        '$' +
        Number(
            datos.total || 0
        ).toFixed(2) +
        ' USD';

}


// ==========================================================
// ABRIR
// ==========================================================

function abrirMiniCarrito() {

    const overlay =
        document.getElementById(
            'lunarmc-mini-cart-overlay'
        );

    const carrito =
        document.getElementById(
            'lunarmc-mini-cart'
        );


    if (
        !overlay ||
        !carrito
    ) {
        return;
    }


    overlay.classList.add(
        'active'
    );

    carrito.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.classList.add(
        'mini-cart-open'
    );

}


// ==========================================================
// CERRAR
// ==========================================================

function cerrarMiniCarrito() {

    const overlay =
        document.getElementById(
            'lunarmc-mini-cart-overlay'
        );

    const carrito =
        document.getElementById(
            'lunarmc-mini-cart'
        );


    if (
        !overlay ||
        !carrito
    ) {
        return;
    }


    overlay.classList.remove(
        'active'
    );

    carrito.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.classList.remove(
        'mini-cart-open'
    );

}


// ==========================================================
// ESCAPAR TEXTO RECIBIDO DE PHP
// ==========================================================

function escaparMiniCarrito(valor) {

    return String(
        valor ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );

}

/* ==========================================
   LUNARMC - CAMBIO DE TEMA ADMIN
   ========================================== */

(() => {
    const body = document.body;
    const button = document.getElementById('admin-theme-toggle');

    // Solo funciona en el panel de administracion
    if (!body.classList.contains('admin-dashboard') || !button) {
        return;
    }

    const saved = localStorage.getItem('lunarmc-admin-theme');

    function applyTheme(light) {
        body.classList.toggle('admin-light', light);

        button.textContent = light
            ? '🌙 Modo oscuro'
            : '☀️ Modo claro';

        button.setAttribute('aria-pressed', String(light));
    }

    // Recuperar la preferencia guardada
    applyTheme(saved === 'light');

    button.addEventListener('click', () => {
        const light = !body.classList.contains('admin-light');

        applyTheme(light);

        localStorage.setItem(
            'lunarmc-admin-theme',
            light ? 'light' : 'dark'
        );
    });
})();

/* ==========================================
   LUNARMC - ICONOS DE BOTONES
   ========================================== */

document.addEventListener("DOMContentLoaded", () => {
    const iconos = {
        ".products-btn-primary": `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                <path d="M17 21v-8H7v8"/>
                <path d="M7 3v5h8"/>
            </svg>
        `,
        ".products-btn-edit": `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 20h9"/>
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
            </svg>
        `,
        ".products-btn-delete": `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 6h18"/>
                <path d="M8 6V4h8v2"/>
                <path d="M5 6l1 15h12l1-15"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
        `,
        ".products-back": `
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M19 12H5"/>
                <path d="m12 19-7-7 7-7"/>
            </svg>
        `
    };

    document.querySelectorAll(".admin-products .products-btn, .admin-products .products-back")
        .forEach((boton) => {
            if (boton.querySelector("svg")) return;

            let svg = "";

            if (boton.classList.contains("products-btn-delete")) {
                svg = iconos[".products-btn-delete"];
            } else if (boton.classList.contains("products-btn-primary")) {
                svg = boton.textContent.includes("Nuevo")
                    ? `<svg viewBox="0 0 24 24" aria-hidden="true">
                           <path d="M12 5v14M5 12h14"/>
                       </svg>`
                    : iconos[".products-btn-primary"];
            } else if (boton.classList.contains("products-btn-edit")) {
                svg = boton.textContent.includes("Cancelar")
                    ? `<svg viewBox="0 0 24 24" aria-hidden="true">
                           <path d="M18 6 6 18M6 6l12 12"/>
                       </svg>`
                    : iconos[".products-btn-edit"];
            } else if (boton.classList.contains("products-back")) {
                svg = iconos[".products-back"];
            }

            if (svg) {
                boton.insertAdjacentHTML("afterbegin", svg);
            }
        });
});

/* ==========================================
   LUNARMC - ICONOS DEL FORMULARIO
   ========================================== */

document.addEventListener("DOMContentLoaded", () => {
    const iconosFormulario = {
        codigo: '<path d="M5 9h14M4 15h14M11 3 7 21M17 3l-4 18"/>',
        nombre: '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>',
        categoria_id: '<path d="M12 2 2 7l10 5 10-5-10-5ZM2 12l10 5 10-5M2 17l10 5 10-5"/>',
        precio: '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h2"/>',
        activo: '<rect x="2" y="7" width="20" height="10" rx="5"/><circle cx="16" cy="12" r="3"/>',
        descripcion: '<path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8l-5-5H8Z"/><path d="M16 3v5h5M8 13h8M8 17h8"/>'
    };

    document.querySelectorAll(
        ".admin-products .products-form .products-field"
    ).forEach((campo) => {
        const control = campo.querySelector("input, select, textarea");
        const label = campo.querySelector("label");

        if (!control || !label) return;

        const dibujo = iconosFormulario[control.id];
        if (!dibujo || label.querySelector("svg")) return;

        const svg = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "svg"
        );

        svg.setAttribute("viewBox", "0 0 24 24");
        svg.setAttribute("aria-hidden", "true");
        svg.innerHTML = dibujo;

        label.prepend(svg);
    });
});

/* ==========================================
   LUNARMC - MODAL PARA ELIMINAR PRODUCTOS
   ========================================== */

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("products-delete-modal");
    if (!modal) return;

    const nombreModal = document.getElementById("products-modal-name");
    const botonCancelar = document.getElementById("products-modal-cancel");
    const botonConfirmar = document.getElementById("products-modal-confirm");

    let formularioPendiente = null;
    let botonAnterior = null;

    function cerrarModal() {
        modal.hidden = true;
        formularioPendiente = null;
        document.body.classList.remove("products-modal-open");
        botonAnterior?.focus();
    }

    document.querySelectorAll(".products-delete-form").forEach((formulario) => {
        formulario.addEventListener("submit", (evento) => {
            evento.preventDefault();

            formularioPendiente = formulario;
            botonAnterior = document.activeElement;

            const fila = formulario.closest("tr");
            const nombre = fila?.querySelector("td:nth-child(3)")?.textContent.trim()
                || "este producto";

            nombreModal.textContent = nombre;

            modal.hidden = false;
            document.body.classList.add("products-modal-open");
            botonCancelar.focus();
        });
    });

    botonCancelar.addEventListener("click", cerrarModal);

    botonConfirmar.addEventListener("click", () => {
        if (!formularioPendiente) return;

        botonConfirmar.disabled = true;

        // Envia el POST original con su token CSRF
        HTMLFormElement.prototype.submit.call(formularioPendiente);
    });

    modal.addEventListener("click", (evento) => {
        if (evento.target === modal) cerrarModal();
    });

    document.addEventListener("keydown", (evento) => {
        if (modal.hidden) return;

        if (evento.key === "Escape") {
            cerrarModal();
        }

        if (evento.key === "Tab") {
            const botones = [botonCancelar, botonConfirmar];
            const primero = botones[0];
            const ultimo = botones[1];

            if (evento.shiftKey && document.activeElement === primero) {
                evento.preventDefault();
                ultimo.focus();
            } else if (!evento.shiftKey && document.activeElement === ultimo) {
                evento.preventDefault();
                primero.focus();
            }
        }
    });
});

/* =====================================================
   LUNARMC - ANIMACIONES DE APARICION REPETIBLES
   Una sola lógica para todas las secciones y las cabezas.
   ===================================================== */
(function () {
    function iniciar() {
        const home = document.querySelector('.home-page .lunar-home-v2');
        if (!home || home.dataset.animacionUnica === '1') return;
        home.dataset.animacionUnica = '1';

        const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const elementos = [...home.querySelectorAll(
            '.lh-hero-inner, .lh-shop-copy, .lh-panel, .lh-section-head, ' +
            '.lh-category, .lh-info, .lh-disclaimer, .lh-existing-content'
        )];
        const meta = home.querySelector('.lh-goal');
        const barra = meta?.querySelector('.lh-progress > span');
        const numero = meta?.querySelector('.lh-goal-number strong');
        const progreso = meta?.querySelector('.lh-progress');
        const porcentaje = Math.max(0, Math.min(100, Number(progreso?.getAttribute('aria-valuenow')) || 0));
        const cabezas = [...home.querySelectorAll('.lh-recent-content img')];
        let animacionNumero = 0;
        let metaActiva = false;

        // Borrar clases antiguas que pueden bloquear la visibilidad.
        home.querySelectorAll('.lh-panel').forEach(el => {
            el.classList.remove('lh-spook-visible', 'lh-scroll-visible', 'lh-repeat-visible');
        });
        home.querySelector('.lh-stats')?.classList.remove('lh-spook-ready', 'lh-scroll-ready', 'lh-repeat-ready', 'lh-scroll-synced');

        cabezas.forEach((img, i) => {
            img.style.setProperty('--lh-head-delay', `${Math.min(i * 75, 1100)}ms`);
            img.classList.add('lh-head-entrada');
        });

        function detenerMeta() {
            metaActiva = false;
            if (animacionNumero) cancelAnimationFrame(animacionNumero);
            animacionNumero = 0;
            if (barra) {
                barra.style.setProperty('transition', 'none', 'important');
                barra.style.setProperty('width', '0%', 'important');
            }
            if (numero) numero.textContent = '0%';
        }

        function animarMeta() {
            if (metaActiva) return;
            metaActiva = true;
            if (barra) {
                barra.style.setProperty('transition', 'none', 'important');
                barra.style.setProperty('width', '0%', 'important');
                void barra.offsetWidth;
                requestAnimationFrame(() => {
                    if (!metaActiva) return;
                    barra.style.setProperty('transition', 'width 1500ms cubic-bezier(.2,.8,.2,1)', 'important');
                    barra.style.setProperty('width', `${porcentaje}%`, 'important');
                });
            }
            if (numero) {
                const inicio = performance.now();
                function actualizar(tiempo) {
                    if (!metaActiva) return;
                    const t = Math.min(1, (tiempo - inicio) / 1500);
                    const suavizado = 1 - Math.pow(1 - t, 3);
                    numero.textContent = `${Math.round(porcentaje * suavizado)}%`;
                    if (t < 1) animacionNumero = requestAnimationFrame(actualizar);
                    else animacionNumero = 0;
                }
                animacionNumero = requestAnimationFrame(actualizar);
            }
        }

        if (!reducido && 'IntersectionObserver' in window) {
            // Preparar antes de observar; al salir se quita la clase para que se repita.
            elementos.forEach((el, i) => {
                el.classList.remove('lh-visible', 'lh-motion-visible');
                el.classList.add('lh-entrada-repetible');
                el.style.setProperty('--lh-entrada-delay', `${(i % 3) * 75}ms`);
            });
            detenerMeta();
            const observador = new IntersectionObserver(entradas => {
                entradas.forEach(entrada => {
                    const el = entrada.target;
                    if (entrada.isIntersecting) {
                        el.classList.add('lh-entrada-visible');
                        if (el === meta) animarMeta();
                    } else {
                        el.classList.remove('lh-entrada-visible');
                        if (el === meta) detenerMeta();
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -18px 0px' });
            elementos.forEach(el => observador.observe(el));
        } else {
            if (barra) barra.style.setProperty('width', `${porcentaje}%`, 'important');
            if (numero) numero.textContent = `${porcentaje}%`;
        }

        // Mantener el parallax original del dragón y el resplandor del mouse.
        if (!reducido) {
            const hero = home.querySelector('.lh-hero');
            let pendiente = false;
            function actualizarParallax() {
                pendiente = false;
                const y = window.scrollY || 0;
                home.style.setProperty('--lh-parallax-dragon', `${Math.max(-70, Math.min(70, y * .12))}px`);
                home.style.setProperty('--lh-parallax-shop', `${Math.max(-40, Math.min(40, (y - 300) * .055))}px`);
            }
            window.addEventListener('scroll', () => {
                if (!pendiente) {
                    pendiente = true;
                    requestAnimationFrame(actualizarParallax);
                }
            }, { passive: true });
            actualizarParallax();
            hero?.addEventListener('pointermove', event => {
                const r = hero.getBoundingClientRect();
                hero.style.setProperty('--lh-glow-x', `${((event.clientX - r.left) / r.width) * 100}%`);
                hero.style.setProperty('--lh-glow-y', `${((event.clientY - r.top) / r.height) * 100}%`);
            });
        }
        console.log('LunarMC: animaciones de entrada repetibles activadas', elementos.length, cabezas.length);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', iniciar);
    else iniciar();
})();
// LunarMC: visor 3D real, interactivo. No renderiza una imagen 2D de MC-Heads.
(() => {
  'use strict';

  async function iniciarVisor() {
    const canvas = document.getElementById('lunar-skin-3d-canvas');
    const estado = document.getElementById('lunar-skin-3d-status');
    if (!canvas || !estado) return; // Otras páginas de LunarMC no se ven afectadas.

    const mensaje = (texto, error = false) => {
      estado.textContent = texto;
      estado.classList.toggle('lunar-skin-error', error);
      estado.hidden = false;
    };

    if (!window.skinview3d || typeof window.skinview3d.SkinViewer !== 'function') {
      mensaje('No se cargó el visor 3D. Revisá la conexión a Internet o el bloqueador de scripts.', true);
      console.error('LunarMC: skinview3d no disponible; revisá Network en F12.');
      return;
    }

    let urlTemporal = null;
    let visor = null;
    try {
      // Primero comprobar la textura: si Mojang no reconoce el nick,
      // no fingir que Steve es su skin.
      const respuesta = await fetch('skin_texture.php', { credentials: 'same-origin', cache: 'no-store' });
      if (!respuesta.ok) {
        throw new Error(`Textura no disponible (HTTP ${respuesta.status}). Comprobá tu conexión con Mojang; si estás en la red escolar, probá con datos móviles.`);
      }
      const tipo = respuesta.headers.get('content-type') || '';
      if (!tipo.toLowerCase().includes('image/png')) {
        throw new Error('skin_texture.php no devolvió una imagen PNG.');
      }
      const blob = await respuesta.blob();
      urlTemporal = URL.createObjectURL(blob);

      visor = new window.skinview3d.SkinViewer({
        canvas,
        width: 260,
        height: 350
      });
      visor.zoom = 0.85;
      visor.autoRotate = false;

if (visor.controls) {
    visor.controls.enableRotate = false;
    visor.controls.enableZoom = false;
    visor.controls.enablePan = false;
}

if (visor.playerObject) {
    visor.playerObject.rotation.y = 0.45;
}
      
      if (visor.playerObject) visor.playerObject.rotation.y = 0.45;

     await visor.loadSkin(urlTemporal);

canvas.hidden = false;
estado.hidden = true;
      console.info('LunarMC: skin 3D cargada correctamente.');
    } catch (error) {
      console.error('LunarMC: fallo del visor 3D:', error);
      mensaje(error instanceof Error ? error.message : 'No se pudo cargar la skin 3D.', true);
      canvas.hidden = true;
      if (visor) visor.dispose();
    } finally {
      if (urlTemporal) URL.revokeObjectURL(urlTemporal);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciarVisor, { once: true });
  } else {
    iniciarVisor();
  }
})();
