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

                <form
                    class="login-form"
                    method="post"
                    action="#"
                    autocomplete="off"
                >

                    <?= csrf_field() ?>

                    <div class="form-field">

                        <label for="nomina">
                            Nómina
                        </label>

                        <div class="form-control">

                            <input
                                type="text"
                                id="nomina"
                                name="nomina"
                                placeholder="Ingresa tu número de nómina"
                                autocomplete="off"
                                required
                            >

                            <span
                                class="form-control__icon"
                                aria-hidden="true"
                            >
                                ♙
                            </span>

                        </div>

                    </div>


                    <div class="form-field">

                        <label for="curp">
                            CURP
                        </label>

                        <div class="form-control">

                            <input
                                type="text"
                                id="curp"
                                name="curp"
                                placeholder="Ingresa tu CURP"
                                maxlength="18"
                                autocomplete="off"
                                required
                            >

                            <span
                                class="form-control__icon"
                                aria-hidden="true"
                            >
                                ▣
                            </span>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="login-form__submit"
                    >
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

                    <img
                        src="<?= base_url(
                            'assets/asuntos_internos/sistema_reportes/img/logo.png'
                        ) ?>"
                        alt="Coordinación de Asuntos Internos"
                    >

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

                </div>

            </div>

        </div>

    </section>

</main>

<?= $this->endSection() ?>