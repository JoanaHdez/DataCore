<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Acceso | Sistema de Reportes
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<main class="login">

    <section class="login__card">

        <div class="login__panel login__panel--form">

            <div>

                <div class="login__heading">

                    <span class="login__eyebrow">
                        Asuntos Internos
                    </span>

                    <h1>
                        Iniciar sesión
                    </h1>

                    <p>
                        Ingresa tus datos institucionales para acceder
                        al sistema.
                    </p>

                </div>

                <?php if (session()->getFlashdata('error')): ?>

                    <div class="login-form__error">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>

                <?php endif; ?>

                <form class="login-form" method="post" action="<?= base_url('asuntos-internos/reportes/login') ?>"
                    autocomplete="off">

                    <?= csrf_field() ?>

                    <div class="form-field">

                        <label for="nomina">
                            Nómina
                        </label>

                        <div class="form-control">

                            <input type="text" id="nomina" name="nomina" placeholder="Ingresa tu número de nómina"
                                autocomplete="off" required>

                            <span class="form-control__icon" aria-hidden="true">

    <svg
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <circle
            cx="12"
            cy="8"
            r="4"
        />

        <path
            d="M5 20c0-3.8 3.1-6.8 7-6.8s7 3 7 6.8"
        />
    </svg>

</span>

                        </div>

                    </div>


                    <div class="form-field">

                        <label for="curp">
                            CURP
                        </label>

                        <div class="form-control">

                            <input type="text" id="curp" name="curp" placeholder="Ingresa tu CURP" maxlength="18"
                                autocomplete="off" required>

                            <span class="form-control__icon" aria-hidden="true">

    <svg
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <rect
            x="3.5"
            y="5"
            width="17"
            height="14"
            rx="2"
        />

        <circle
            cx="9"
            cy="11"
            r="2"
        />

        <path
            d="M6.5 16c.5-1.7 1.5-2.6 2.5-2.6s2 .9 2.5 2.6"
        />

        <path
            d="M14 10h3.5"
        />

        <path
            d="M14 13h3.5"
        />
    </svg>

</span>

                        </div>

                    </div>


                    <button type="submit" class="login-form__submit">
                        Ingresar
                    </button>

                </form>


                <p class="login__help">
                    Acceso exclusivo para personal autorizado
                    de Asuntos Internos.
                </p>

            </div>

        </div>


        <div class="login__panel login__panel--identity">

            <div class="login__identity-content">

                <div class="login__logo">

                    <img src="<?= base_url(
                                    'assets/asuntos_internos/sistema_reportes/img/logo.png'
                                ) ?>" alt="Coordinación de Asuntos Internos">

                </div>


                <div class="login__identity-text">

                    <span>
                        Policía Municipal
                    </span>

                    <strong>
                        Coordinación de
                        <br>
                        Asuntos Internos
                    </strong>

                    <small>
                        Nezahualcóyotl
                    </small>

                    <div class="login-institucional__logos">

                        <img
                            src="<?= base_url(
                                        'assets/asuntos_internos/sistema_reportes/img/ayuntamiento.png'
                                    ) ?>"
                            alt="Ayuntamiento de Nezahualcóyotl"
                            class="login-institucional__logo login-institucional__logo--ayuntamiento">

                        <span class="login-institucional__logos-separador"></span>

                        <img
                            src="<?= base_url(
                                        'assets/asuntos_internos/sistema_reportes/img/cgsc.png'
                                    ) ?>"
                            alt="Comisaría General de Seguridad Ciudadana"
                            class="login-institucional__logo login-institucional__logo--comisaria">

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?= $this->endSection() ?>